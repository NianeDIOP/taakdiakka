<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CommunityController;
use App\Http\Controllers\DemandeController;
use App\Http\Controllers\FriendController;
use App\Http\Controllers\GalleryController;
use App\Http\Controllers\MemberController;
use App\Http\Controllers\MembersController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\PasswordResetController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RelationController;
use App\Http\Controllers\SubscriptionController;
use App\Http\Controllers\AdminBillingController;
use App\Models\Demande;
use App\Models\Post;
use App\Models\SuccessStory;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    $demandes = Demande::latest('published_at')->take(6)->get();
    $posts    = Post::with(['author.profile', 'postReactions'])
        ->withCount('postComments')
        ->latest('published_at')->take(9)->get();
    $stories  = SuccessStory::latest('id')->take(8)->get();

    $online = \App\Models\User::query()->online()->whereHas('profile')
        ->with('profile')->latest('last_seen_at')->take(8)->get();
    $themes = \App\Http\Controllers\CommunityController::THEMES;

    return view('welcome', compact('demandes', 'posts', 'stories', 'online', 'themes') + ['theme' => 'Tout']);
})->name('home');

/* Plan du site (SEO) */
Route::get('/sitemap.xml', function () {
    $urls = [
        ['loc' => url('/'),                    'freq' => 'daily',   'priority' => '1.0'],
        ['loc' => url('/communaute'),          'freq' => 'daily',   'priority' => '0.9'],
        ['loc' => url('/histoires'),           'freq' => 'weekly',  'priority' => '0.7'],
        ['loc' => url('/tarifs'),              'freq' => 'monthly', 'priority' => '0.7'],
        ['loc' => url('/inscription'),         'freq' => 'monthly', 'priority' => '0.6'],
        ['loc' => url('/connexion'),           'freq' => 'monthly', 'priority' => '0.4'],
    ];
    foreach (\App\Models\Post::whereNotNull('published_at')->latest('published_at')->take(500)->get(['id', 'updated_at']) as $p) {
        $urls[] = ['loc' => url('/communaute/'.$p->id), 'freq' => 'weekly', 'priority' => '0.6', 'lastmod' => optional($p->updated_at)->toAtomString()];
    }
    $xml = '<?xml version="1.0" encoding="UTF-8"?>'."\n".'<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">'."\n";
    foreach ($urls as $u) {
        $xml .= '  <url><loc>'.e($u['loc']).'</loc>';
        if (! empty($u['lastmod'])) $xml .= '<lastmod>'.$u['lastmod'].'</lastmod>';
        $xml .= '<changefreq>'.$u['freq'].'</changefreq><priority>'.$u['priority'].'</priority></url>'."\n";
    }
    return response($xml.'</urlset>', 200, ['Content-Type' => 'application/xml; charset=UTF-8']);
})->name('sitemap');

Route::middleware(['auth', 'members_only'])->group(function () {
    Route::get('/demandes', [DemandeController::class, 'index'])->name('demandes.index');
    Route::delete('/demandes/{demande}', [DemandeController::class, 'destroy'])->whereNumber('demande')->name('demandes.destroy');
    Route::post('/demandes/{demande}/favori', [MemberController::class, 'toggleFavorite'])->whereNumber('demande')->name('favoris.toggle');
    Route::post('/demandes/{demande}/contacter', [MessageController::class, 'contact'])->whereNumber('demande')->name('messages.contact');
    Route::get('/demandes/{demande}', [DemandeController::class, 'show'])->whereNumber('demande')->name('demandes.show');
});

Route::middleware('module:community')->group(function () {
    Route::get('/communaute', [CommunityController::class, 'index'])->name('communaute');
    Route::get('/communaute/charger', [CommunityController::class, 'loadFeed'])->name('community.load');
    Route::get('/communaute/nouveautes', [CommunityController::class, 'newPosts'])->name('community.new');
    Route::post('/communaute/compteurs', [CommunityController::class, 'counters'])->name('community.counters');
    Route::get('/communaute/{post}/commentaires', [CommunityController::class, 'comments'])->whereNumber('post')->name('community.comments');

    Route::middleware('auth')->group(function () {
        Route::post('/communaute', [CommunityController::class, 'store'])->name('community.store')->middleware('throttle:15,1');
        Route::post('/communaute/{post}/reaction', [CommunityController::class, 'react'])->whereNumber('post')->name('community.react');
        Route::post('/communaute/{post}/enregistrer', [CommunityController::class, 'toggleSave'])->whereNumber('post')->name('community.save');
        Route::post('/communaute/{post}/voter', [CommunityController::class, 'vote'])->whereNumber('post')->name('community.vote');
        Route::post('/communaute/{post}/commentaires', [CommunityController::class, 'storeComment'])->whereNumber('post')->name('community.comment')->middleware('throttle:30,1');
        Route::post('/communaute/commentaires/{comment}/like', [CommunityController::class, 'likeComment'])->whereNumber('comment')->name('community.comment.like');
        Route::post('/communaute/{post}/signaler', [CommunityController::class, 'report'])->whereNumber('post')->name('community.report');
        Route::post('/communaute/commentaires/{comment}/signaler', [CommunityController::class, 'reportComment'])->whereNumber('comment')->name('community.comment.report');
    });

    Route::get('/communaute/{post}', function (Post $post) {
        $similaires = Post::where('id', '!=', $post->id)
            ->where('theme', $post->theme)
            ->latest('published_at')
            ->take(3)
            ->get();

        if ($similaires->count() < 3) {
            $similaires = Post::where('id', '!=', $post->id)
                ->latest('published_at')
                ->take(3)
                ->get();
        }

        return view('communaute.show', compact('post', 'similaires'));
    })->name('communaute.show');
});

Route::middleware(['auth', 'admin'])->prefix('admin')->group(function () {
    Route::get('/', [\App\Http\Controllers\AdminController::class, 'dashboard'])->name('admin.dashboard');

    // Modération
    Route::get('/moderation', [\App\Http\Controllers\AdminController::class, 'moderation'])->name('admin.moderation');
    Route::put('/moderation/{report}', [\App\Http\Controllers\AdminController::class, 'resolveReport'])->whereNumber('report')->name('admin.moderation.resolve');
    Route::delete('/moderation/{report}/contenu', [\App\Http\Controllers\AdminController::class, 'deleteContent'])->whereNumber('report')->name('admin.moderation.delete');

    // Communauté (publications & commentaires)
    Route::get('/communaute', [\App\Http\Controllers\AdminController::class, 'community'])->name('admin.community');
    Route::delete('/communaute/post/{post}', [\App\Http\Controllers\AdminController::class, 'deletePost'])->whereNumber('post')->name('admin.community.post.delete');
    Route::delete('/communaute/commentaire/{comment}', [\App\Http\Controllers\AdminController::class, 'deleteComment'])->whereNumber('comment')->name('admin.community.comment.delete');

    // Blocages entre membres
    Route::get('/blocages', [\App\Http\Controllers\AdminController::class, 'blocks'])->name('admin.blocks');

    // Audience & analytics
    Route::get('/audience', [\App\Http\Controllers\AdminController::class, 'analytics'])->name('admin.analytics');
    Route::delete('/blocages/{blocker}/{blocked}', [\App\Http\Controllers\AdminController::class, 'removeBlock'])->whereNumber('blocker')->whereNumber('blocked')->name('admin.blocks.remove');

    // Modules & règles premium (super admin)
    Route::middleware('super_admin')->group(function () {
        Route::get('/modules', [\App\Http\Controllers\AdminController::class, 'modules'])->name('admin.modules');
        Route::post('/modules', [\App\Http\Controllers\AdminController::class, 'saveModules'])->name('admin.modules.save');

        // Publicités
        Route::get('/publicites', [\App\Http\Controllers\AdminAdsController::class, 'index'])->name('admin.ads');
        Route::post('/publicites', [\App\Http\Controllers\AdminAdsController::class, 'store'])->name('admin.ads.store');
        Route::put('/publicites/{ad}', [\App\Http\Controllers\AdminAdsController::class, 'update'])->whereNumber('ad')->name('admin.ads.update');
        Route::delete('/publicites/{ad}', [\App\Http\Controllers\AdminAdsController::class, 'destroy'])->whereNumber('ad')->name('admin.ads.destroy');

        // Pièces d'or & cadeaux (admin)
        Route::get('/pieces-cadeaux', [\App\Http\Controllers\AdminCoinsController::class, 'index'])->name('admin.coins');
        Route::put('/pieces-cadeaux/packs/{coinPack}', [\App\Http\Controllers\AdminCoinsController::class, 'updatePack'])->whereNumber('coinPack')->name('admin.coins.pack.update');
        Route::put('/pieces-cadeaux/cadeaux/{gift}', [\App\Http\Controllers\AdminCoinsController::class, 'updateGift'])->whereNumber('gift')->name('admin.coins.gift.update');
        Route::put('/pieces-cadeaux/cost', [\App\Http\Controllers\AdminCoinsController::class, 'updateCost'])->name('admin.coins.cost.update');

        // Abonnements & monétisation
        Route::get('/formules', [AdminBillingController::class, 'plans'])->name('admin.billing.plans');
        Route::put('/formules/{plan}', [AdminBillingController::class, 'updatePlan'])->whereNumber('plan')->name('admin.billing.plan.update');
        Route::put('/boosts/{boostPack}', [AdminBillingController::class, 'updateBoost'])->whereNumber('boostPack')->name('admin.billing.boost.update');
        Route::get('/abonnements', [AdminBillingController::class, 'subscriptions'])->name('admin.billing.subscriptions');
        Route::put('/abonnements/{subscription}/annuler', [AdminBillingController::class, 'cancelSubscription'])->whereNumber('subscription')->name('admin.billing.subscription.cancel');
        Route::get('/paiement', [AdminBillingController::class, 'payment'])->name('admin.billing.payment');
        Route::post('/paiement', [AdminBillingController::class, 'savePayment'])->name('admin.billing.payment.save');

        // Paramètres généraux, SEO & journal
        Route::get('/parametres', [\App\Http\Controllers\AdminSettingsController::class, 'general'])->name('admin.settings');
        Route::post('/parametres', [\App\Http\Controllers\AdminSettingsController::class, 'saveGeneral'])->name('admin.settings.save');
        Route::post('/parametres/test-email', [\App\Http\Controllers\AdminSettingsController::class, 'sendTestEmail'])->name('admin.settings.testmail');
        Route::get('/journal', [\App\Http\Controllers\AdminSettingsController::class, 'logs'])->name('admin.logs');

        // Pages légales
        Route::get('/pages', [\App\Http\Controllers\AdminSettingsController::class, 'pages'])->name('admin.pages');
        Route::put('/pages/{page}', [\App\Http\Controllers\AdminSettingsController::class, 'savePage'])->name('admin.pages.save');
    });

    // Contenu : témoignages (admin & modérateur)
    Route::get('/contenu', [\App\Http\Controllers\AdminSettingsController::class, 'content'])->name('admin.content');
    Route::post('/contenu/temoignages', [\App\Http\Controllers\AdminSettingsController::class, 'storeStory'])->name('admin.content.story.store');
    Route::put('/contenu/temoignages/{story}', [\App\Http\Controllers\AdminSettingsController::class, 'updateStory'])->whereNumber('story')->name('admin.content.story.update');
    Route::delete('/contenu/temoignages/{story}', [\App\Http\Controllers\AdminSettingsController::class, 'deleteStory'])->whereNumber('story')->name('admin.content.story.delete');

    // Utilisateurs
    Route::get('/utilisateurs', [\App\Http\Controllers\AdminUserController::class, 'index'])->name('admin.users.index');
    Route::get('/utilisateurs/export', [\App\Http\Controllers\AdminUserController::class, 'export'])->name('admin.users.export');
    Route::get('/utilisateurs/{user}', [\App\Http\Controllers\AdminUserController::class, 'show'])->whereNumber('user')->name('admin.users.show');
    Route::post('/utilisateurs/{user}/suspendre', [\App\Http\Controllers\AdminUserController::class, 'suspend'])->whereNumber('user')->name('admin.users.suspend');
    Route::post('/utilisateurs/{user}/bannir', [\App\Http\Controllers\AdminUserController::class, 'ban'])->whereNumber('user')->name('admin.users.ban');
    Route::post('/utilisateurs/{user}/reactiver', [\App\Http\Controllers\AdminUserController::class, 'reactivate'])->whereNumber('user')->name('admin.users.reactivate');
    Route::post('/utilisateurs/{user}/verifier', [\App\Http\Controllers\AdminUserController::class, 'verify'])->whereNumber('user')->name('admin.users.verify');
    Route::post('/utilisateurs/{user}/mot-de-passe', [\App\Http\Controllers\AdminUserController::class, 'resetPassword'])->whereNumber('user')->name('admin.users.reset');
    Route::delete('/utilisateurs/{user}', [\App\Http\Controllers\AdminUserController::class, 'destroy'])->whereNumber('user')->name('admin.users.destroy');
});

Route::get('/histoires', function () {
    $stories = SuccessStory::latest('id')->get();

    return view('histoires.index', compact('stories'));
})->name('histoires')->middleware('module:stories');

Route::get('/tarifs', [SubscriptionController::class, 'tarifs'])->name('tarifs');

// Pages légales publiques (slugs fixes)
Route::get('/{page:slug}', [\App\Http\Controllers\PageController::class, 'show'])
    ->where('page', 'conditions|confidentialite|mentions-legales')
    ->name('page');

/* ---------- Abonnements & boosts ---------- */
Route::middleware('auth')->group(function () {
    Route::post('/abonnement/{plan}/souscrire', [SubscriptionController::class, 'checkout'])->whereNumber('plan')->name('subscribe.checkout');
    Route::get('/abonnement/simulation', [SubscriptionController::class, 'simulate'])->name('subscribe.simulate');
    Route::get('/abonnement/{subscription}/retour', [SubscriptionController::class, 'callback'])->whereNumber('subscription')->name('subscribe.callback');
    Route::get('/abonnement/{subscription}/merci', [SubscriptionController::class, 'success'])->whereNumber('subscription')->name('subscribe.success');
    Route::post('/boost/{boostPack}/acheter', [SubscriptionController::class, 'boostCheckout'])->whereNumber('boostPack')->name('boost.checkout');
    Route::get('/boost/{boost}/retour', [SubscriptionController::class, 'boostCallback'])->whereNumber('boost')->name('boost.callback');
});

/* ---------- Authentification ---------- */
Route::middleware('guest')->group(function () {
    Route::get('/inscription', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/inscription', [AuthController::class, 'register'])->middleware('throttle:8,1');
    Route::get('/connexion', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/connexion', [AuthController::class, 'login'])->middleware('throttle:6,1');

    Route::get('/mot-de-passe-oublie', [PasswordResetController::class, 'showForgot'])->name('password.request');
    Route::post('/mot-de-passe-oublie', [PasswordResetController::class, 'sendLink'])->name('password.email')->middleware('throttle:4,1');
    Route::get('/reinitialiser/{token}', [PasswordResetController::class, 'showReset'])->name('password.reset');
    Route::post('/reinitialiser', [PasswordResetController::class, 'reset'])->name('password.update')->middleware('throttle:6,1');
});
Route::post('/deconnexion', [AuthController::class, 'logout'])->name('logout');

/* ---------- Espace membre ---------- */
Route::middleware(['auth', 'members_only'])->prefix('espace')->group(function () {
    Route::get('/', [MembersController::class, 'home'])->name('dashboard');

    // Parcours d'accueil guidé (nouveaux membres)
    Route::get('/bienvenue', [\App\Http\Controllers\OnboardingController::class, 'show'])->name('onboarding');
    Route::post('/bienvenue/profil', [\App\Http\Controllers\OnboardingController::class, 'saveProfile'])->name('onboarding.profile');
    Route::post('/bienvenue/photo', [\App\Http\Controllers\OnboardingController::class, 'savePhoto'])->name('onboarding.photo');
    Route::post('/bienvenue/terminer', [\App\Http\Controllers\OnboardingController::class, 'finish'])->name('onboarding.finish');

    Route::get('/decouvrir', [MembersController::class, 'discover'])->name('members.discover');
    Route::get('/en-ligne', [MembersController::class, 'online'])->name('members.online');
    Route::get('/membres/{user}', [MembersController::class, 'show'])->whereNumber('user')->name('members.show');

    Route::get('/ma-demande', [DemandeController::class, 'mine'])->name('demandes.mine');
    Route::post('/ma-demande/publier', [DemandeController::class, 'publish'])->name('demandes.publish');
    Route::put('/ma-demande/statut', [DemandeController::class, 'status'])->name('demandes.status');
    Route::get('/profil', [ProfileController::class, 'show'])->name('profile.show');
    Route::get('/profil/modifier', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profil', [ProfileController::class, 'update'])->name('profile.update');
    Route::post('/profil/photos', [GalleryController::class, 'store'])->middleware('module:gallery')->name('gallery.store');
    Route::delete('/profil/photos/{photo}', [GalleryController::class, 'destroy'])->whereNumber('photo')->middleware('module:gallery')->name('gallery.destroy');

    Route::get('/matchs', [RelationController::class, 'matchs'])->name('matchs');
    Route::get('/visiteurs', [RelationController::class, 'visitors'])->name('visitors');
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::post('/membres/{user}/interet', [RelationController::class, 'toggleInterest'])->whereNumber('user')->name('interests.toggle');
    Route::post('/membres/{user}/suivre', [RelationController::class, 'toggleFollow'])->whereNumber('user')->name('follow.toggle');
    Route::post('/membres/{user}/discuter', [RelationController::class, 'startConversation'])->whereNumber('user')->name('messages.start');
    Route::post('/membres/{user}/bloquer', [RelationController::class, 'block'])->whereNumber('user')->name('members.block');
    Route::delete('/membres/{user}/bloquer', [RelationController::class, 'unblock'])->whereNumber('user')->name('members.unblock');
    Route::post('/membres/{user}/signaler', [RelationController::class, 'report'])->whereNumber('user')->name('members.report');

    // Demandes d'ami
    Route::get('/demandes-ami', [FriendController::class, 'index'])->name('friends.index');
    Route::post('/membres/{user}/ami', [FriendController::class, 'request'])->whereNumber('user')->name('friends.request');
    Route::post('/demandes-ami/{friendRequest}/accepter', [FriendController::class, 'accept'])->whereNumber('friendRequest')->name('friends.accept');
    Route::post('/demandes-ami/{friendRequest}/refuser', [FriendController::class, 'decline'])->whereNumber('friendRequest')->name('friends.decline');
    Route::delete('/demandes-ami/{friendRequest}', [FriendController::class, 'cancel'])->whereNumber('friendRequest')->name('friends.cancel');

    Route::middleware('module:messaging')->group(function () {
        Route::get('/messages', [MessageController::class, 'index'])->name('messages');
        Route::get('/messages/{conversation}', [MessageController::class, 'show'])->whereNumber('conversation')->name('messages.show');
        Route::post('/messages/{conversation}', [MessageController::class, 'store'])->whereNumber('conversation')->name('messages.store')->middleware('throttle:30,1');
    });
    Route::get('/favoris', [MemberController::class, 'favoris'])->name('favoris');
    Route::get('/enregistrements', [CommunityController::class, 'saved'])->name('community.saved');
    Route::get('/abonnement', [MemberController::class, 'subscription'])->name('subscription.mine');
    Route::get('/verification', [MemberController::class, 'verification'])->middleware('module:verification')->name('verification');
    Route::post('/verification', [MemberController::class, 'verify'])->middleware('module:verification')->name('verification.submit');
    Route::get('/parametres', [MemberController::class, 'settings'])->name('settings');
    Route::put('/parametres/compte', [MemberController::class, 'updateAccount'])->name('settings.account');
    Route::put('/parametres/mot-de-passe', [MemberController::class, 'updatePassword'])->name('settings.password');
    Route::put('/parametres/confidentialite', [MemberController::class, 'updatePrivacy'])->name('settings.privacy');
    Route::put('/parametres/notifications', [MemberController::class, 'updateNotifications'])->name('settings.notifications');
    Route::delete('/parametres/compte', [MemberController::class, 'destroyAccount'])->name('settings.destroy');

    // Pièces d'or & cadeaux
    Route::get('/pieces-dor', [\App\Http\Controllers\CoinController::class, 'shop'])->name('coins.shop');
    Route::get('/pieces-dor/historique', [\App\Http\Controllers\CoinController::class, 'history'])->name('coins.history');
    Route::post('/pieces-dor/{coinPack}/acheter', [\App\Http\Controllers\CoinController::class, 'checkout'])->whereNumber('coinPack')->name('coins.checkout');
    Route::get('/pieces-dor/{transaction}/retour', [\App\Http\Controllers\CoinController::class, 'callback'])->whereNumber('transaction')->name('coins.callback');
    Route::post('/pieces-dor/spotlight', [\App\Http\Controllers\CoinController::class, 'spotlight'])->name('coins.spotlight');
    Route::get('/cadeaux/liste', [\App\Http\Controllers\CoinController::class, 'gifts'])->name('gifts.list');
    Route::post('/membres/{user}/cadeau', [\App\Http\Controllers\CoinController::class, 'sendGift'])->whereNumber('user')->name('gifts.send');
});
