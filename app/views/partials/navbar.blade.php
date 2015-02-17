<div class="row">
    <div class="container">
        <div class="navbar navbar-inverse">
            <div class="navbar-header">
                <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-responsive-collapse">
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
                <a class="navbar-brand" href="{{ URL::to('/') }}"><i class="fa fa-lightbulb-o"></i></a>
            </div>
            <div class="navbar-collapse collapse navbar-responsive-collapse">
                <ul class="nav navbar-nav">
                    @include(Config::get('laravel-menu::views.bootstrap-items'), array('items' => $adminNavBar->roots()))
                </ul>
                <ul class="nav navbar-nav navbar-right">
                    @include(Config::get('laravel-menu::views.bootstrap-items'), array('items' => $authNavBar->roots()))
                    <form class="navbar-form navbar-left">
                        <input type="text" class="form-control col-lg-8" placeholder='Поиск (не работает)'>
                    </form>
                </ul>
            </div>
        </div>
    </div>
</div>