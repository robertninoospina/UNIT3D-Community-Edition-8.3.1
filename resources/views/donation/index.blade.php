@extends('layout.default')

@section('title')
    <title>Apoya Al Sitio - {{ config('other.title') }}</title>
@endsection

@section('meta')
    <meta name="description" content="Apoya Al Sitio" />
@endsection

@section('breadcrumbs')
    <li class="breadcrumb--active">Apoya Al Sitio</li>
@endsection

@section('content')
    <section x-data class="panelV2">
        <h2 class="panel__heading">Planes VIP {{ config('other.title') }}</h2>
        <div class="panel__body">
            <p>{{ config('donation.description') }}</p>
            <div class="donation-packages">
                @foreach ($packages as $package)
                    <div class="donation-package__wrapper">
                        <div class="donation-package">
                            <div class="donation-package__header">
                                <div class="donation-package__name">{{ $package->name }}</div>
                                <div class="donation-package__price-days">
                                    <span class="donation-package__price">
                                        {{ $package->cost }} {{ config('donation.currency') }}
                                    </span>
                                    <span class="donation-package__separator">-</span>
                                    <span class="donation-package__days">
                                        @if ($package->donor_value === null)
                                            Lifetime
                                        @else
                                            {{ $package->donor_value }} Days
                                        @endif
                                    </span>
                                </div>
                                <div class="donation-package__description">
                                    {{ $package->description }}
                                </div>
                            </div>
                            <div class="donation-package__benefits-list">
                                <ol class="benefits-list">
                                    @if ($package->donor_value === null)
                                        <li>Unlimited Download Slots</li>
                                    @endif

                                    @if ($package->donor_value === null)
                                        <li>Custom User Icon</li>
                                    @endif

                                    <li>Acceso Global a Freeleech</li>
                                    <li>Proteccion Contra Advertencias Automaticas (Uso Responsable)</li>
                                    <li
                                        style="
                                            background-image: url(/img/sparkels.gif);
                                            width: auto;
                                        "
                                    >
                                        Efecto De Destello en Tu Nombre de Usuario
                                    </li>
                                    <li>
                                        Icono Exclusivo para Miembros Donadores
                                        @if ($package->donor_value === null)
                                            <i
                                                id="lifeline"
                                                class="fal fa-star"
                                                title="Lifetime Donor"
                                            ></i>
                                        @else
                                            <i class="fal fa-star text-gold" title="Donor"></i>
                                        @endif
                                    </li>
                                    <li>
                                        Presume Que Apoyas al Equipo Lat-Team
                                        {{ config('other.title') }}
                                    </li>
                                    @if ($package->upload_value !== null)
                                        <li>
                                            {{ App\Helpers\StringHelper::formatBytes($package->upload_value) }}
                                            Credito para Subidas
                                        </li>
                                    @endif

                                    @if ($package->bonus_value !== null)
                                        <li>
                                            {{ number_format($package->bonus_value) }} Puntos Adicionales
                                        </li>
                                    @endif

                                    @if ($package->invite_value !== null)
                                        <li>{{ $package->invite_value }} Invitaciones Disponibles</li>
                                    @endif
                                </ol>
                            </div>
                            <div class="donation-package__footer">
                                <p class="form__group form__group--horizontal">
                                    <button
                                        class="form__button form__button--filled form__button--centered"
                                        x-on:click.stop="$refs.dialog{{ $package->id }}.showModal()"
                                    >
                                        <i class="fas fa-handshake"></i>
                                        Activar
                                    </button>
                                </p>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        @foreach ($packages as $package)
            <dialog class="dialog" x-ref="dialog{{ $package->id }}">
                <h4 class="dialog__heading">Activa Tu Plan Y Ayudanos A Continuar Mejorando $ {{ $package->cost }} USD</h4>
                <form
                    class="dialog__form"
                    method="POST"
                    action="{{ route('donations.store') }}"
                    x-on:click.outside="$refs.dialog{{ $package->id }}.close()"
                >
                    @csrf
                    <span class="text-success text-center">
                        Para hacer Tu Donacion Selecciona Uno De Los Siguientes Metodos De Pago disponibles:
                    </span>
                    <div class="form__group--horizontal">
                        @foreach ($gateways->sortBy('position') as $gateway)
                            <p class="form__group">
                                <input
                                    class="form__text"
                                    type="text"
                                    disabled
                                    value="{{ $gateway->address }}"
                                    id="{{ 'gateway-' . $gateway->id }}"
                                />
                                <label
                                    for="{{ 'gateway-' . $gateway->id }}"
                                    class="form__label form__label--floating"
                                >
                                    {{ $gateway->name }}
                                </label>
                            </p>
                        @endforeach

                        <p class="text-info">
                            Envia
                            <strong>
                                $ {{ $package->cost }} {{ config('donation.currency') }}
                            </strong>
                            Al metodo de pago que elijas. Por favor Anota El Numero De Transaccion O Descarga El Comprobante De La Misma, Por Si Se Requiere Luego.
                        </p>
                    </div>
                    <div class="form__group--horizontal">
                        <p class="form__group">
                            <input
                                class="form__text"
                                type="text"
                                disabled
                                value="{{ $package->cost }}"
                                id="package-cost"
                            />
                            <label for="package-cost" class="form__label form__label--floating">
                                Valor
                            </label>
                        </p>
                        <p class="form__group">
                            <input
                                class="form__text"
                                type="text"
                                value=""
                                id="proof"
                                name="transaction"
                            />
                            <label for="proof" class="form__label form__label--floating">
                                Envia Tu Nombre De Usuario En EL Tracker Y Discord (Super Importante)
                            </label>
                        </p>
                    </div>
                    <span class="text-warning">
                        * Las Activaciones Pueden tardar Hasta 24 Horas (tratamos De No Demorar Tanto).
                    </span>
                    <p class="form__group">
                        <input type="hidden" name="package_id" value="{{ $package->id }}" />
                        <button class="form__button form__button--filled">Activar Plan</button>
                        <button
                            formmethod="dialog"
                            formnovalidate
                            class="form__button form__button--outlined"
                        >
                            {{ __('common.cancel') }}
                        </button>
                    </p>
                </form>
            </dialog>
        @endforeach
    </section>
@endsection
