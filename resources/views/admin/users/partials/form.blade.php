@csrf

@if(isset($user))
    @method('PUT')
@endif

<div class="form-group">
    <label>Username</label>
    <input
        type="text"
        name="username"
        value="{{ old('username',$user->username ?? '') }}"
        required>
</div>

<div class="form-group">
    <label>Nama Lengkap</label>
    <input
        type="text"
        name="full_name"
        value="{{ old('full_name',$user->full_name ?? '') }}"
        required>
</div>

<div class="form-group">
    <label>Email</label>
    <input
        type="email"
        name="email"
        value="{{ old('email',$user->email ?? '') }}"
        required>
</div>

<div class="form-group">
    <label>No HP</label>
    <input
        type="text"
        name="phone_number"
        value="{{ old('phone_number',$user->phone_number ?? '') }}">
</div>

<div class="form-group">
    <label>Password</label>

    <input
        type="password"
        name="password">

    @isset($user)
        <small>Kosongkan jika tidak diubah</small>
    @endisset
</div>

<div class="form-group">
    <label>Role</label>

    <select name="role">

        <option value="user"
        {{ old('role',$user->role ?? '')=='user'?'selected':'' }}>
            User
        </option>

        <option value="mentor"
        {{ old('role',$user->role ?? '')=='mentor'?'selected':'' }}>
            Mentor
        </option>

        <option value="admin"
        {{ old('role',$user->role ?? '')=='admin'?'selected':'' }}>
            Admin
        </option>

    </select>
</div>

<button type="submit" class="btn-primary">
    Simpan
</button>