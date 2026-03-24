<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<x-head title="Log In" />
<body>
    <main>
        <div class="max-w-lg py-8 mx-auto mt-12 border">
            <div class="text-center text-4xl uppercase font-bold">Log In</div>
            @auth
            <div class="mx-auto text-4xl text-primary">You're already logged in.</div>
            @else
            <form class="pt-12 flex flex-col space-y-4 max-w-md mx-auto" action="/admin-{{ config('app.initials') }}/login" method="post">
                @csrf
                <input class="bg-white border border-black rounded px-2 py-1 text-lg" type="text" name="user" placeholder="Username">
                <input class="bg-white border border-black rounded px-2 py-1 text-lg" type="password" name="password" id="password">
                @error('user')
                    <p class="bg-red-600 text-white rounded w-fit px-2 py-1">{{$message}}</p>
                @enderror
                <button class="w-fit px-2 py-1 border bg-gray-200 text-lg rounded hover:opacity-60 duration-300 cursor-pointer" type="submit">Log In</button>
            </form>
            @endauth
        </div>
    </main> 
</body>
</html>