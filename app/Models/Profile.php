<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Profile extends Model
{
    protected $fillable = [
        'user_id', 'phone', 'verification_level', 'gender', 'birthdate', 'religion', 'practice', 'marital_status',
        'has_children', 'children_count', 'wants_children', 'union_type', 'education',
        'profession', 'languages', 'height_cm', 'complexion', 'region', 'bio', 'seeking', 'photo',
    ];

    /** Ordre des paliers de vérification. */
    public const VERIF_RANK = ['Bronze' => 1, 'Argent' => 2, 'Or' => 3];

    protected $casts = [
        'birthdate'    => 'date',
        'has_children' => 'boolean',
        'languages'    => 'array',
    ];

    /* ---- Options proposées (menus déroulants) ---- */
    public const OPTIONS = [
        'gender'         => ['Homme', 'Femme'],
        'religion'       => ['Islam', 'Christianisme', 'Autre'],
        'practice'       => ['Pratiquant(e)', 'Modéré(e)', 'Non pratiquant(e)'],
        'marital_status' => ['Célibataire', 'Divorcé(e)', 'Veuf(ve)'],
        'wants_children' => ['Oui', 'Non', 'Plus tard'],
        'union_type'     => ['Monogame', 'Polygame', 'Indifférent'],
        'education'      => ['Sans diplôme', 'Brevet / BFEM', 'Baccalauréat', 'Bac +2', 'Licence', 'Master', 'Doctorat'],
        'complexion'     => ['Clair', 'Caramel', 'Foncé'],
        'languages'      => ['Wolof', 'Français', 'Anglais', 'Arabe', 'Pulaar', 'Sérère', 'Diola'],
        'region'         => ['Dakar', 'Thiès', 'Saint-Louis', 'Touba', 'Ziguinchor', 'Rufisque', 'Diaspora'],
        'seeking'        => ['Une épouse', 'Un époux'],
    ];

    /** Présentations pré-rédigées (sélection rapide, sans tout taper). */
    public const BIO_TEMPLATES = [
        "Croyant(e) et posé(e), je recherche une union sincère et bénie, fondée sur le respect, la foi et des valeurs communes.",
        "Simple, travailleur(se) et bienveillant(e), je souhaite fonder un foyer paisible avec une personne honnête et sérieuse.",
        "Ouvert(e) d'esprit et attaché(e) à mes valeurs, je cherche une relation sérieuse menant au mariage, dans le respect mutuel.",
        "Discret(e) et de bonne intention, je souhaite rencontrer une personne sincère pour bâtir une famille unie, inch'Allah.",
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /** Âge calculé depuis la date de naissance. */
    public function getAgeAttribute(): ?int
    {
        return $this->birthdate ? $this->birthdate->age : null;
    }

    /** Pourcentage de complétion du profil. */
    public function getCompletionAttribute(): int
    {
        $fields = ['gender', 'birthdate', 'religion', 'practice', 'marital_status',
            'wants_children', 'union_type', 'education', 'profession', 'languages',
            'height_cm', 'complexion', 'region', 'bio', 'photo'];

        $filled = 0;
        foreach ($fields as $f) {
            $v = $this->{$f};
            if (! empty($v) || (is_array($v) && count($v))) {
                $filled++;
            }
        }

        return (int) round($filled / count($fields) * 100);
    }

    /** Score de compatibilité entre deux profils (0–100). */
    public static function compatibility(?self $a, ?self $b): int
    {
        if (! $a || ! $b) {
            return 0;
        }
        $s = 0;
        if ($a->region && $a->region === $b->region) {
            $s += 28;
        }
        if ($a->religion && $a->religion === $b->religion) {
            $s += 22;
        }
        if ($a->practice && $a->practice === $b->practice) {
            $s += 14;
        }
        if ($a->marital_status && $a->marital_status === $b->marital_status) {
            $s += 8;
        }
        if ($a->wants_children && $a->wants_children === $b->wants_children) {
            $s += 8;
        }
        if ($a->age && $b->age) {
            $s += max(0, 20 - abs($a->age - $b->age) * 2);
        }

        return min(99, 50 + (int) round($s * 49 / 100));
    }

    /** Badges de personnalité auto-générés depuis le profil rempli. */
    public function getPersonalityBadgesAttribute(): array
    {
        $b = [];
        if ($this->practice === 'Pratiquant(e)') {
            $b[] = ['icon' => '🕌', 'label' => 'Spirituel'];
        }
        if ($this->education && in_array($this->education, ['Master', 'Doctorat', 'Licence'])) {
            $b[] = ['icon' => '🎓', 'label' => 'Études sup.'];
        }
        if ($this->children_count > 0 || $this->wants_children === 'Oui') {
            $b[] = ['icon' => '👨‍👩‍👧', 'label' => 'Famille'];
        }
        if ($this->profession && mb_strlen($this->profession) > 2) {
            $b[] = ['icon' => '💼', 'label' => 'Actif'];
        }
        if ($this->region === 'Diaspora') {
            $b[] = ['icon' => '✈️', 'label' => 'Diaspora'];
        }
        if ($this->bio && mb_strlen($this->bio) > 80) {
            $b[] = ['icon' => '✍️', 'label' => 'Expressif'];
        }
        if ($this->union_type === 'Monogame') {
            $b[] = ['icon' => '💍', 'label' => 'Monogame'];
        }
        if (is_array($this->languages) && count($this->languages) >= 3) {
            $b[] = ['icon' => '🗣️', 'label' => 'Polyglotte'];
        }

        return array_slice($b, 0, 3);
    }
}
