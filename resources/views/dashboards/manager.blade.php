<h1>Manager Dashboard</h1>
<p>Welcome to the Music School Management System.</p>

<form method="POST" action="{{ route('logout') }}">
    @csrf

    <button type="submit" class="btn btn-danger">
        Logout
    </button>
</form>