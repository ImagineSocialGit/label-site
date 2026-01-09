<x-layout title="Log In" :universalData="$univseralData">
        
        @auth
        <div class="mx-auto text-4xl text-primary">You're already logged in.</div>
        @else
        <h1 class="text-center text-secondary text-4xl font-tai uppercase font-bold">Log In</h1>
        <form class="pt-12 flex flex-col space-y-4 max-w-md mx-auto" action="/admin-{{ config('app.initials') }}/login" method="post">
            @csrf
            <input class="bg-white rounded px-2 py-1 text-lg" type="text" name="user" placeholder="Username">
            <input class="bg-white rounded px-2 py-1 text-lg" type="password" name="password" id="password">
            @error('user')
                <p class="bg-red-600 text-white rounded w-fit px-2 py-1">{{$message}}</p>
            @enderror
            <button class="w-fit px-4 py-2 bg-secondary text-alt-black text-xl rounded hover:opacity-60 duration-300 cursor-pointer" type="submit">Log In</button>
        </form>
        @endauth

</x-layout>