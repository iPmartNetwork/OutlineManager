<!doctype html>
<html lang="en">

@include('includes.head')

<body data-bs-theme="dark">

<div class="container mt-2 mt-md-5">
    @include('includes.header')

    <main>
        @yield('content')
    </main>

    @include('includes.footer')
</div>

@yield('footer-stuff')

</body>
</html>
