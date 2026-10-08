<?php
namespace App\Console\Commands;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Validator;
class CreatePortfolioAdmin extends Command {
    protected $signature = 'portfolio:admin {email?}';
    protected $description = 'Crear una cuenta administradora (contraseña oculta, mínimo 12 caracteres)';
    public function handle(): int {
        $email=$this->argument('email') ?: $this->ask('Correo');
        $name=$this->ask('Nombre','Romina Elizabeth Jaimes');
        $password=$this->secret('Contraseña (mínimo 12 caracteres)');
        $validator=Validator::make(compact('email','name','password'),['email'=>'required|email|unique:users,email','name'=>'required|string|max:255','password'=>'required|string|min:12']);
        if($validator->fails()){foreach($validator->errors()->all() as $error)$this->error($error);return self::FAILURE;}
        $user=new User; $user->name=$name; $user->email=$email; $user->password=$password; $user->is_admin=true;$user->save();
        $this->info('Cuenta creada. Ingresá en /admin.');return self::SUCCESS;
    }
}
