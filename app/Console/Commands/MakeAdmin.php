<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class MakeAdmin extends Command
{
    protected $signature = 'taak:make-admin
        {email : Adresse e-mail du compte}
        {--name= : Nom affiché (création)}
        {--password= : Mot de passe (création)}
        {--role=super_admin : super_admin ou moderateur}
        {--revoke : Retire le rôle admin au lieu de l\'attribuer}';

    protected $description = 'Crée un compte administrateur pur (sans profil membre) ou promeut/rétrograde un compte existant.';

    public function handle(): int
    {
        $email = $this->argument('email');
        $role = $this->option('role');

        if (! in_array($role, ['super_admin', 'moderateur'], true)) {
            $this->error("Rôle invalide : {$role} (attendu : super_admin ou moderateur).");

            return self::FAILURE;
        }

        $user = User::where('email', $email)->first();

        /* --- Révocation --- */
        if ($this->option('revoke')) {
            if (! $user) {
                $this->error("Aucun compte avec l'e-mail {$email}.");

                return self::FAILURE;
            }
            $user->update(['role' => null]);
            $this->info("Rôle admin retiré pour {$email}. Le compte est désormais un membre standard.");

            return self::SUCCESS;
        }

        /* --- Création --- */
        if (! $user) {
            $password = $this->option('password');
            $name = $this->option('name');
            if (! $name || ! $password) {
                $this->error('Pour créer un compte, fournissez --name et --password.');

                return self::FAILURE;
            }
            $user = User::create([
                'name'     => $name,
                'email'    => $email,
                'password' => Hash::make($password),
                'role'     => $role,
                'is_admin' => true,
                'status'   => 'active',
            ]);
            $this->info("Compte administrateur créé : {$email} ({$role}).");

            return self::SUCCESS;
        }

        /* --- Promotion d'un compte existant : on détache son profil membre --- */
        $user->update(['role' => $role, 'is_admin' => true]);
        $this->detachMemberData($user);

        $this->info("{$email} est désormais {$role}. Son profil membre a été détaché.");

        return self::SUCCESS;
    }

    /** Supprime toutes les données « membre » d'un compte devenu admin. */
    private function detachMemberData(User $user): void
    {
        $id = $user->id;

        $user->demandes()->delete();
        $user->photos()->delete();
        $user->profile()->delete();

        // Tables pivot / sociales résiduelles
        DB::table('interests')->where('user_id', $id)->orWhere('target_user_id', $id)->delete();
        DB::table('follows')->where('follower_id', $id)->orWhere('followed_id', $id)->delete();
        DB::table('profile_views')->where('viewer_id', $id)->orWhere('viewed_id', $id)->delete();
        DB::table('favorites')->where('user_id', $id)->delete();
        \App\Models\FriendRequest::where('sender_id', $id)->orWhere('receiver_id', $id)->delete();
    }
}
