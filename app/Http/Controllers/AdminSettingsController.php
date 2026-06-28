<?php

namespace App\Http\Controllers;

use App\Models\AdminLog;
use App\Models\Setting;
use App\Models\SuccessStory;
use Illuminate\Http\Request;

class AdminSettingsController extends Controller
{
    /** Champs gérés : clé => [valeur par défaut, type, groupe]. */
    private const FIELDS = [
        // Identité & marque
        'site.name'            => ['TàakDiàkka', 'string', 'general'],
        'site.tagline'         => ['La rencontre bénie', 'string', 'general'],
        'site.logo'            => ['', 'string', 'general'],
        'site.contact_email'   => ['', 'string', 'general'],
        'site.contact_phone'   => ['', 'string', 'general'],
        'site.address'         => ['', 'string', 'general'],
        // Inscriptions & accès
        'site.registration_open' => [true, 'bool', 'access'],
        'site.maintenance'     => [false, 'bool', 'access'],
        // Réseaux sociaux
        'social.facebook'      => ['', 'string', 'social'],
        'social.instagram'     => ['', 'string', 'social'],
        'social.whatsapp'      => ['', 'string', 'social'],
        'social.tiktok'        => ['', 'string', 'social'],
        'social.linkedin'      => ['', 'string', 'social'],
        'social.youtube'       => ['', 'string', 'social'],
        // SEO & analytics
        'seo.meta_title'       => ['', 'string', 'seo'],
        'seo.meta_description' => ['', 'string', 'seo'],
        'seo.keywords'         => ['', 'string', 'seo'],
        'seo.og_image'         => ['', 'string', 'seo'],
        'seo.ga_id'            => ['', 'string', 'seo'],
        'seo.pixel_id'         => ['', 'string', 'seo'],
        // E-mails (expéditeur / SMTP)
        'mail.from_name'       => ['TàakDiàkka', 'string', 'mail'],
        'mail.from_email'      => ['', 'string', 'mail'],
        'mail.host'            => ['', 'string', 'mail'],
        'mail.port'            => ['', 'string', 'mail'],
        'mail.username'        => ['', 'string', 'mail'],
        'mail.password'        => ['', 'string', 'mail'],
        'mail.encryption'      => ['tls', 'string', 'mail'],
    ];

    /* ---------------- Paramètres généraux & SEO ---------------- */

    public function general()
    {
        $values = [];
        foreach (self::FIELDS as $key => [$default, $type, $group]) {
            $values[$key] = Setting::get($key, $default);
        }

        return view('admin.settings', compact('values'));
    }

    public function saveGeneral(Request $request)
    {
        $data = $request->validate([
            'site_name'        => ['required', 'string', 'max:80'],
            'site_tagline'     => ['nullable', 'string', 'max:160'],
            'logo'             => ['nullable', 'image', 'mimes:png,jpg,jpeg,webp,svg', 'max:2048'],
            'contact_email'    => ['nullable', 'email', 'max:160'],
            'contact_phone'    => ['nullable', 'string', 'max:40'],
            'address'          => ['nullable', 'string', 'max:200'],
            'meta_title'       => ['nullable', 'string', 'max:120'],
            'meta_description' => ['nullable', 'string', 'max:255'],
            'keywords'         => ['nullable', 'string', 'max:255'],
            'og_image'         => ['nullable', 'image', 'mimes:png,jpg,jpeg,webp', 'max:3072'],
            'ga_id'            => ['nullable', 'string', 'max:40'],
            'pixel_id'         => ['nullable', 'string', 'max:40'],
            'from_name'        => ['nullable', 'string', 'max:80'],
            'from_email'       => ['nullable', 'email', 'max:160'],
            'mail_host'        => ['nullable', 'string', 'max:160'],
            'mail_port'        => ['nullable', 'string', 'max:8'],
            'mail_username'    => ['nullable', 'string', 'max:160'],
            'mail_password'    => ['nullable', 'string', 'max:160'],
            'mail_encryption'  => ['nullable', 'in:tls,ssl,none'],
        ] + array_fill_keys(['social_facebook', 'social_instagram', 'social_whatsapp', 'social_tiktok', 'social_linkedin', 'social_youtube'], ['nullable', 'string', 'max:255']));

        // Identité
        Setting::put('site.name', $data['site_name'], 'string', 'general');
        Setting::put('site.tagline', $data['site_tagline'] ?? '', 'string', 'general');
        Setting::put('site.contact_email', $data['contact_email'] ?? '', 'string', 'general');
        Setting::put('site.contact_phone', $data['contact_phone'] ?? '', 'string', 'general');
        Setting::put('site.address', $data['address'] ?? '', 'string', 'general');

        if ($request->hasFile('logo')) {
            Setting::put('site.logo', $this->storeUpload($request->file('logo'), 'site-logo'), 'string', 'general');
        }
        if ($request->hasFile('og_image')) {
            Setting::put('seo.og_image', $this->storeUpload($request->file('og_image'), 'og-image'), 'string', 'seo');
        }

        // Accès
        Setting::put('site.registration_open', $request->boolean('registration_open'), 'bool', 'access');
        Setting::put('site.maintenance', $request->boolean('maintenance'), 'bool', 'access');

        // Réseaux sociaux
        foreach (['facebook', 'instagram', 'whatsapp', 'tiktok', 'linkedin', 'youtube'] as $net) {
            Setting::put("social.$net", $data["social_$net"] ?? '', 'string', 'social');
        }

        // SEO
        Setting::put('seo.meta_title', $data['meta_title'] ?? '', 'string', 'seo');
        Setting::put('seo.meta_description', $data['meta_description'] ?? '', 'string', 'seo');
        Setting::put('seo.keywords', $data['keywords'] ?? '', 'string', 'seo');
        Setting::put('seo.ga_id', $data['ga_id'] ?? '', 'string', 'seo');
        Setting::put('seo.pixel_id', $data['pixel_id'] ?? '', 'string', 'seo');

        // E-mails
        Setting::put('mail.from_name', $data['from_name'] ?? '', 'string', 'mail');
        Setting::put('mail.from_email', $data['from_email'] ?? '', 'string', 'mail');
        Setting::put('mail.host', $data['mail_host'] ?? '', 'string', 'mail');
        Setting::put('mail.port', $data['mail_port'] ?? '', 'string', 'mail');
        Setting::put('mail.username', $data['mail_username'] ?? '', 'string', 'mail');
        Setting::put('mail.password', $data['mail_password'] ?? '', 'string', 'mail');
        Setting::put('mail.encryption', $data['mail_encryption'] ?? 'tls', 'string', 'mail');

        AdminLog::record($request->user()->id, 'settings_general', null, null, 'Paramètres de la plateforme mis à jour');

        return back()->with('status', 'Paramètres enregistrés.');
    }

    /** Envoie un e-mail de test pour vérifier la configuration. */
    public function sendTestEmail(Request $request)
    {
        $data = $request->validate(['test_email' => ['required', 'email']]);

        try {
            \Illuminate\Support\Facades\Mail::raw(
                "Ceci est un e-mail de test envoyé depuis l'administration de " . Setting::siteName() . ".\n\n"
                . "Si vous le recevez, votre configuration e-mail fonctionne correctement. 🤲",
                fn ($m) => $m->to($data['test_email'])->subject('E-mail de test — ' . Setting::siteName()),
            );
        } catch (\Throwable $e) {
            return back()->with('status', '❌ Échec de l\'envoi : ' . $e->getMessage());
        }

        $driver = config('mail.default');
        $note = $driver === 'log' ? ' (mailer « log » : voir storage/logs/laravel.log)' : '';

        AdminLog::record($request->user()->id, 'mail_test', null, null, 'E-mail de test envoyé à ' . $data['test_email']);

        return back()->with('status', '✅ E-mail de test envoyé à ' . $data['test_email'] . $note);
    }

    /** Enregistre un fichier image dans public/img et renvoie son nom. */
    private function storeUpload(\Illuminate\Http\UploadedFile $file, string $prefix): string
    {
        $name = $prefix . '-' . now()->timestamp . '.' . $file->getClientOriginalExtension();
        $file->move(public_path('img'), $name);

        return $name;
    }

    /* ---------------- Contenu : success stories ---------------- */

    public function content()
    {
        $stories = SuccessStory::latest('id')->get();

        return view('admin.content', compact('stories'));
    }

    public function storeStory(Request $request)
    {
        $data = $this->validateStory($request);
        SuccessStory::create($data);

        AdminLog::record($request->user()->id, 'story_create', 'SuccessStory', null, $data['couple']);

        return back()->with('status', 'Témoignage ajouté.');
    }

    public function updateStory(Request $request, SuccessStory $story)
    {
        $data = $this->validateStory($request);
        $story->update($data);

        AdminLog::record($request->user()->id, 'story_update', 'SuccessStory', $story->id, $data['couple']);

        return back()->with('status', 'Témoignage mis à jour.');
    }

    public function deleteStory(Request $request, SuccessStory $story)
    {
        $couple = $story->couple;
        $story->delete();

        AdminLog::record($request->user()->id, 'story_delete', 'SuccessStory', $story->id, $couple);

        return back()->with('status', 'Témoignage supprimé.');
    }

    private function validateStory(Request $request): array
    {
        $data = $request->validate([
            'couple'      => ['required', 'string', 'max:120'],
            'initials'    => ['nullable', 'string', 'max:8'],
            'location'    => ['nullable', 'string', 'max:80'],
            'badge_label' => ['nullable', 'string', 'max:60'],
            'quote'       => ['required', 'string', 'max:600'],
            'badge_heart' => ['nullable', 'boolean'],
        ]);
        $data['badge_heart'] = $request->boolean('badge_heart');
        $data['badge_icon'] = 'i-rings';

        return $data;
    }

    /* ---------------- Pages légales ---------------- */

    public function pages()
    {
        $order = array_keys(\App\Models\Page::LEGAL);
        $pages = \App\Models\Page::all()
            ->sortBy(fn ($p) => array_search($p->slug, $order) === false ? 99 : array_search($p->slug, $order))
            ->values();

        return view('admin.pages', compact('pages'));
    }

    public function savePage(Request $request, \App\Models\Page $page)
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:120'],
            'body'  => ['nullable', 'string', 'max:20000'],
        ]);

        $page->update($data);

        AdminLog::record($request->user()->id, 'page_update', 'Page', $page->id, $page->title);

        return back()->with('status', 'Page « ' . $page->title . ' » enregistrée.');
    }

    /* ---------------- Journal d'activité ---------------- */

    public function logs()
    {
        $logs = AdminLog::with('admin')->latest()->paginate(30);

        return view('admin.logs', compact('logs'));
    }
}
