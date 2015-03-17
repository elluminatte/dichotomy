<div class="container">
        <div class="navbar navbar-inverse">
            <div class="navbar-header">
                <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-responsive-collapse">
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
                <a class="navbar-brand" href="{{ URL::to('/') }}"><i class="fa fa-adjust"></i> dichotomy</a>
            </div>
            <div class="navbar-collapse collapse navbar-responsive-collapse">
                <ul class="nav navbar-nav">
                    <li><a href="{{ URL::to('/') }}">Главная</a></li>
                </ul>
                @if(isset($adminNavBar))
                <ul class="nav navbar-nav">
                    @include(Config::get('laravel-menu::views.bootstrap-items'), array('items' => $adminNavBar->roots()))
                </ul>
                @endif
                @if(isset($userNavBar))
                <ul class="nav navbar-nav">
                    @include(Config::get('laravel-menu::views.bootstrap-items'), array('items' => $userNavBar->roots()))
                </ul>
                @endif
                <ul class="nav navbar-nav navbar-right">
                    @if(isset($authNavBar))
                    @include(Config::get('laravel-menu::views.bootstrap-items'), array('items' => $authNavBar->roots()))
                    @endif
                    @if( \Auth::user() && \Entrust::hasRole('user'))
                        {{ Form::open(['route' => 'search', 'class' => 'navbar-form navbar-left']) }}
                        {{ Form::text('search_text', null, ['id' => 'search_text', 'class' => 'form-control col-lg-6', 'placeholder' => 'Поиск','rel'=>"tooltip", 'title' => 'Введите сюда код ОКВЭД, название проблемной ситуации или название задачи для решения и нажмите Enter', 'data-toggle'=>"tooltip", 'data-placement'=>'bottom']) }}
                        {{ Form::close() }}
                    @endif
                </ul>
            </div>
        </div>
</div>