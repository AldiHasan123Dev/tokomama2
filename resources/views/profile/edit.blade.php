<x-Layout.layout>
<x-keuangan.card-keuangan>
        <x-slot:tittle>Edit Profile</x-slot:tittle>
        <!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Profil</title>
    <style>
        h2 {
            text-align: center;
            color: #333;
            margin-bottom: 25px;
        }

        label {
            display: block;
            margin-bottom: 6px;
            font-weight: 600;
            color: #444;
        }

        input[type="text"],
        input[type="email"],
        input[type="password"],
        select,
        textarea {
            width: 100%;
            padding: 10px 12px;
            border: 1px solid #ccc;
            border-radius: 6px;
            margin-bottom: 15px;
            font-size: 14px;
            box-sizing: border-box;
            transition: border-color 0.3s;
        }

        input:focus, select:focus, textarea:focus {
            border-color: #007bff;
            outline: none;
        }

        textarea {
            resize: vertical;
        }

        .btn-container {
            display: flex;
            justify-content: space-between;
        }

        .btn {
            padding: 10px 20px;
            font-size: 14px;
            border-radius: 6px;
            cursor: pointer;
            border: none;
            transition: 0.3s;
        }

        .btn-primary {
            background-color: #007bff;
            color: #fff;
        }

        .btn-primary:hover {
            background-color: #0056b3;
        }

        .btn-secondary {
            background-color: #6c757d;
            color: #fff;
        }

        .btn-secondary:hover {
            background-color: #5a6268;
        }

        .alert {
            padding: 10px 15px;
            border-radius: 6px;
            margin-bottom: 15px;
            color: #fff;
        }

        .alert-success {
            background-color: #28a745;
        }

        .alert-error {
            background-color: #dc3545;
        }
    </style>
</head>
<body>

<div class="container">
    <h2>Edit Profil</h2>

    {{-- Pesan sukses --}}
    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    {{-- Pesan error --}}
    @if ($errors->any())
        <div class="alert alert-error">
            <ul style="margin: 0; padding-left: 20px;">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('profile.update1', $user->id) }}" method="POST">
        @csrf
        @method('PUT')
        @if ($user->role->name === "SUPER ADMIN")
            
        <label for="role_id">Role</label>
        <select name="role_id" id="role_id" required>
            <option value="">-- Pilih Role --</option>
            @foreach($roles as $role)
            <option value="{{ $role->id }}" {{ $user->role_id == $role->id ? 'selected' : '' }}>
                {{ $role->name }}
            </option>
            @endforeach
        </select>
        @endif

        <label for="name">Nama Lengkap</label>
        <input type="text" id="name" value="{{ old('name', $user->name) }}" readonly>

        <label for="email">Alamat Email</label>
        <input type="email" id="email" value="{{ old('email', $user->email) }}" readonly>

        <label for="password">Password Baru</label>
        <input type="password" name="password" id="password" placeholder="Kosongkan jika tidak ingin mengganti">

        <label for="phone">Nomor Telepon</label>
        <input type="text" id="phone" value="{{ old('phone', $user->phone) }}">

        <label for="address">Alamat</label>
        <textarea readonly id="address" rows="3">{{ old('address', $user->address) }}</textarea>

        <div class="btn-container">
            <a href="{{ url()->previous() }}" class="btn btn-secondary">Kembali</a>
            <button type="submit" class="btn btn-primary">Simpan</button>
        </div>
    </form>
</div>

</body>
</html>

</x-keuangan.card-keuangan>
</x-Layout.layout>