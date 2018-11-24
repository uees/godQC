<nav class="navbar navbar-default navbar-fixed-top">
    <div class="container-fluid">
        <div class="navbar-header">
            <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#app-navbar-collapse" aria-expanded="false">
                <span class="sr-only">Toggle Navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>

            <a class="navbar-brand" href="{{ url('/') }}">
                {{ config('app.name', 'Laravel') }}
            </a>
        </div>

        <div class="collapse navbar-collapse" id="app-navbar-collapse">
            <!-- Left Side Of Navbar -->
            <ul class="nav navbar-nav">
                <li class="{{ request()->routeIs('fqc.*') ? ' active' : '' }}"><a href="{{ url('fqc') }}">成品检测</a></li>
                <li class="{{ request()->routeIs('iqc.*') ? ' active' : '' }}"><a href="{{ url('iqc') }}">来料检测</a></li>
                <li class="{{ request()->routeIs('categories.*') ? ' active' : '' }}"><a href="{{ url('categories') }}">分类</a></li>
                <li class="{{ request()->routeIs('products.*') ? ' active' : '' }}"><a href="{{ url('products') }}">产品</a></li>
                <li class="{{ request()->routeIs('customers.*') ? ' active' : '' }}"><a href="{{ url('customers') }}">客户</a></li>
                <li class="{{ request()->routeIs('test-methods.*') ? ' active' : '' }}"><a href="{{ url('test-methods') }}">检测方法</a></li>
                <li class="{{ request()->routeIs('test-ways.*') ? ' active' : '' }}"><a href="{{ url('test-ways') }}">检测流程</a></li>
            </ul>

            <!-- Right Side Of Navbar -->
            <ul class="nav navbar-nav navbar-right">
                <!-- Authentication Links -->
                @guest
                    <li><a href="{{ route('login') }}">Login</a></li>
                    <li><a href="{{ route('register') }}">Register</a></li>
                @else
                    <li class="dropdown">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button"
                           aria-expanded="false" aria-haspopup="true" v-pre>
                            {{ Auth::user()->name }} <span class="caret"></span>
                        </a>

                        <ul class="dropdown-menu">
                            <li>
                                <a href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                    Logout
                                </a>

                                <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                                    {{ csrf_field() }}
                                </form>
                            </li>
                        </ul>
                    </li>
                @endguest
            </ul>
        </div> <!--/.nav-collapse -->
    </div>
</nav>
