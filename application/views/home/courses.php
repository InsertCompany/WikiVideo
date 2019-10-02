{% extends "layouts/index.html" %}

{% block title %}Index{% endblock %}
{% block head %}
    {{ parent() }}
{% endblock %}
{% block content %}
    <div class="courses">
        <div class="container">
            <h3 class="mb-5 text-center ">Cursos cadastrados na plataforma.</h3>
            <div class="row">
                <a class="btn-toggle-courser-filter"><i class="fas fa-bars"></i>
                </a>
                <aside class="col-lg-3 col-md-1 courser-filter-desktop">
                    <h5>Filtrar por cursos</h5>
                    <form>
                        <label for="title">Titulo do Curso:</label>
                        <input type="text" class="form-control" name="title">
                        <label for="category">Ordernar por:</label>
                        <select class="custom-select">
                            <option>
                                Avaliação crescente
                            </option>
                            <option>
                                Avaliação decrescente
                            </option>
                            <option>
                                Numero de matriculas crescente
                            </option>
                            <option>
                                Numero de matriculas decrescente
                            </option>
                            <option>
                                Cursos Mais novos
                            </option>
                            <option>
                                Cursos Mais Antigos
                            </option>
                        </select>
                        <label for="category">Categoria</label>
                        <select class="custom-select">
                            <option>
                                Categoria
                            </option>
                        </select>
                        <label for="category">Sub-Categoria</label>
                        <select class="custom-select">
                            <option>
                                Sub-Categoria
                            </option>
                        </select>
                        <input type="submit" class="btn btn-primary mt-3 btn-block">
                    </form>
                </aside><!-- aside.col-lg-3 -->
                <div class="col-lg-9 col-md-11">
                    <div class="card mb-3">
                        <div class="card-body">
                            <div class="row card-course-header text-center">
                                <h5 class="card-title col-12 col-md-12">Titulo do curso</h5>
                                <p class="col-12 col-md-12">Categoria do curso</p>
                            </div>
                            <p>DLorem ipsum dolor sit amet, consectetur adipiscing elit. In placerat semper volutpat. Ut fringilla ultricies ultricies. Nunc imperdiet, nisl quis faucibus aliquet.</p>
                            <div class="d-flex justify-content-between">
                                <div class="row">
                                    <div class="col-12 col-md-2"><img class="course-author-img" src="{{base_url}}public/img/user.png"></div>
                                    <div class="col-12 col-md-10">
                                        <label>Nome Professor</label>
                                        <p>00/00/0000</p>
                                    </div>
                                </div>
                                <div class="container-stars">
                                    <p>Avaliação</p>
                                    <span>
                                        <i class="fas fa-star"></i>
                                    </span>
                                    <span>
                                        <i class="far fa-star"></i>
                                    </span>
                                    <span>
                                        <i class="far fa-star"></i>
                                    </span>
                                    <span>
                                        <i class="far fa-star"></i>
                                    </span>
                                    <span>
                                        <i class="far fa-star"></i>
                                    </span>
                                </div>
                            </div><!-- div.d-flex-->
                            <a href="{{base_url}}course?id=" class="btn btn-block btn-primary">Mais Informações</a>
                        </div><!-- div.card-body-->
                    </div><!-- div.card -->
                    <div>
                        <nav>
                            <ul class="pagination d-flex justify-content-center">
                                <li class="page-item"><a class="page-link" href="#">Previous</a></li>
                                <li class="page-item active "><a class="page-link" href="#">1</a></li>
                                <li class="page-item"><a class="page-link" href="#">2</a></li>
                                <li class="page-item"><a class="page-link" href="#">3</a></li>
                                <li class="page-item"><a class="page-link" href="#">Next</a></li>
                            </ul><!-- ul.pagination -->
                        </nav><!-- nav -->
                    </div>
                </div> <!-- div.col-lg-9-->
            </div> <!-- div.row -->
        </div><!-- div.container -->
    </div><!-- div.courses -->

{% endblock %}