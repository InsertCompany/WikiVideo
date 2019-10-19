    <!DOCTYPE html>
    <html>
        <head>
            {% block head %}
                <title>{% block title %}{% endblock %} - Wikivideo</title>
                <link href="{{ base_url }}public/css/bootstrap.min.css" rel="stylesheet">
                <link href="{{ base_url }}public/css/style.css"rel="stylesheet">

            {% endblock %}
        </head>
        <body>
            <head>
                <nav class="navbar navbar-expand-lg navbar-light bg-light">
                    <a class="navbar-brand" href="#">Wikivideo</a>
                    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#conteudoNavbarSuportado" aria-controls="conteudoNavbarSuportado" aria-expanded="false" aria-label="Alterna navegação">
                        <span class="navbar-toggler-icon"></span>
                    </button><!-- button.nav-bar-toggler -->
                    <div class="collapse navbar-collapse" id="conteudoNavbarSuportado">
                         <ul class="navbar-nav d-flex justify-content-center">
                           <li class="nav-item dropdown">
                               <a class="nav-link dropdown-toggle" href="#" id="navbarCourses" role="button" data-toggle="dropdown" aria-haspopup="true">Cursos</a>
                            </li><!-- li.nav-item-->
                        </ul><!-- ul.navbar-nav -->
                        <ul class="navbar-nav ml-auto">
                            <a href="#">Entrar/Registrar-se</a>
                        </ul><!-- ul.navbar-nav -->
                    </div><!-- div.navbar-collapse -->
                </nav> <!-- nav.navbar -->
            </head>
            <div id="content">{% block content %}{% endblock %}</div>
            <div id="footer">
            </div>
            <script src="{{base_url}}public/js/jquery.min.js">
        </body>
    </html>