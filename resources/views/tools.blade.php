@extends('layouts.app')
@section('content')
<header class="page-header">
    <h2>Herramientas para la API</h2>
</header>
<div class="row">

<div class="col-12 col-sm-6 col-md-6 col-lg-6 col-xl-3">
        <section class="card mb-4">
            <div class="card-body bg-secondary">
                <div class="widget-summary">
                    <div class="widget-summary-col widget-summary-col-icon">
                        <div class="summary-icon">
                            <i class="fa fa-robot"></i>
                        </div>
                    </div>
                    <div class="widget-summary-col">
                        <div class="summary">
                            <h4 class="title" style="word-break: normal;">ChatGPT BOT FULLSYS TECNOLOGÍA SANTA MARTA</h4>
                            {{-- <div class="info">
                                <strong class="amount">1281</strong>
                            </div> --}}
                        </div>
                        <div class="summary-footer">
                            <a href="https://chatgpt.com/g/g-6757cbff7bf08191a45c4ee5ff55bc22-facturacion-electronica-dian-colombia" target="_blank" class="text-uppercase">Ir</a>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
    
    <div class="col-12 col-sm-6 col-md-6 col-lg-6 col-xl-3">
        <section class="card mb-4">
            <div class="card-body bg-secondary">
                <div class="widget-summary">
                    <div class="widget-summary-col widget-summary-col-icon">
                        <div class="summary-icon">
                            <i class="fa fa-server"></i>
                        </div>
                    </div>
                    <div class="widget-summary-col">
                        <div class="summary">
                            <h4 class="title" style="word-break: normal;">Test API Swagger</h4>
                            {{-- <div class="info">
                                <strong class="amount">1281</strong>
                            </div> --}}
                        </div>
                        <div class="summary-footer">
                            <a href="{{route('documentation')}}" target="_blank" class="text-uppercase">Ir</a>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
    <div class="col-12 col-sm-6 col-md-6 col-lg-6 col-xl-3">
        <section class="card mb-4">
            <div class="card-body bg-secondary">
                <div class="widget-summary">
                    <div class="widget-summary-col widget-summary-col-icon">
                        <div class="summary-icon">
                            <i class="fas fa-rocket"></i>
                        </div>
                    </div>
                    <div class="widget-summary-col">
                        <div class="summary">
                            <h4 class="title" style="word-break: normal;">Documentación Postman</h4>
                            {{-- <div class="info">
                                <strong class="amount">1281</strong>
                            </div> --}}
                        </div>
                        <div class="summary-footer">
                            <a href="https://documenter.getpostman.com/view/1431398/2sAY4uCido#intro" target="_blank" class="text-uppercase">Ir</a>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>

    <div class="col-12 col-sm-6 col-md-6 col-lg-6 col-xl-3">
        <section class="card mb-4">
            <div class="card-body bg-secondary">
                <div class="widget-summary">
                    <div class="widget-summary-col widget-summary-col-icon">
                        <div class="summary-icon">
                            <i class="fab fa-android"></i>
                        </div>
                    </div>
                    <div class="widget-summary-col">
                        <div class="summary">
                            <h4 class="title" style="word-break: normal;">APP Android</h4>
                            {{-- <div class="info">
                                <strong class="amount">1281</strong>
                            </div> --}}
                        </div>
                        <div class="summary-footer">
                            <a href="https://facturalatam.com/apk/apidian.apk" class="text-uppercase">Descargar</a>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
</div>
@endsection
