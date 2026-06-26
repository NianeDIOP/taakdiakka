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
}
