@guest
    <p><a href="{{ route('login') }}">Se connecter</a></p>
    <p><a href="{{ route('signup') }}">S'incrire</a></p>
@else
    <p><a href="{{ route('customers.index', ['is_prospect' => true]) }}">Prospect</a></p>
    <p><a href="{{ route('customers.index') }}">Clients</a></p>
    <p><a href="{{ route('establishments.index') }}">Centre</a></p>
    <p><a href="{{ route('activities.index') }}">Activités</a></p>
    <p><a href="{{ route('passes.index') }}">Passes</a></p>
    <p><a href="{{ route('roles.index') }}">Roles</a></p>
    <p><a href="{{ route('routes.list') }}">Routes</a></p>
    <p><a href="{{ route('compte.index') }}">Compte</a></p>
@endguest
