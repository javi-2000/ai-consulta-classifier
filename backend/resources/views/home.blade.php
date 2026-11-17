<!doctype html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Ecig Logística</title>
    <link rel="icon" href="{{ asset('favicon.ico') }}" type="image/x-icon">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: #f5f7fb;
        }

        h1 {
            font-weight: 700;
            color: #1f2937;
        }

        .lead-text {
            font-size: 1.05rem;
            color: #6b7280;
            max-width: 800px;
        }

        .card {
            border: none;
            border-radius: 18px;
            overflow: hidden;
        }

        .card-header {
            background: white;
            border-bottom: 1px solid #eef2f7;
            font-weight: 600;
            padding: 1rem 1.5rem;
        }

        .card-body {
            padding: 1.75rem;
        }

        textarea.form-control {
            border-radius: 12px;
            padding: 1rem;
            min-height: 150px;
            resize: vertical;
        }

        textarea:focus {
            box-shadow: 0 0 0 .2rem rgba(13, 110, 253, .12);
        }

        .btn-primary {
            padding: .7rem 1.8rem;
            border-radius: 10px;
            font-weight: 600;
        }

        .table {
            margin-bottom: 0;
        }

        .table thead th {
            border: none;
            background: #f8fafc;
            color: #6b7280;
            font-size: .82rem;
            text-transform: uppercase;
            letter-spacing: .05em;
            padding: 1rem;
        }

        .table tbody td {
            padding: 1rem;
            vertical-align: middle;
            border-color: #eef2f7;
        }

        .table tbody tr:hover {
            background: #fafcff;
        }

        .message-cell {
            max-width: 420px;
            color: #374151;
            line-height: 1.45;
        }

        .badge-category {
            padding: .55em .9em;
            border-radius: 999px;
            font-weight: 600;
            font-size: .78rem;
        }

        .status-success {
            display: inline-block;
            padding: .45rem .9rem;
            border-radius: 999px;
            background: #dcfce7;
            color: #166534;
            border: 1px solid #86efac;
            font-weight: 600;
        }

        .status-error {
            display: inline-block;
            padding: .45rem .9rem;
            border-radius: 999px;
            background: #fee2e2;
            color: #991b1b;
            border: 1px solid #fecaca;
            font-weight: 600;
        }

        .status-timeout {
            display: inline-block;
            padding: .45rem .9rem;
            border-radius: 999px;
            background: #fef3c7;
            color: #92400e;
            border: 1px solid #fde68a;
            font-weight: 600;
        }

        .status-no-credits {
            display: inline-block;
            padding: .45rem .9rem;
            border-radius: 999px;
            background: #ede9fe;
            color: #5b21b6;
            border: 1px solid #c4b5fd;
            font-weight: 600;
        }
    </style>
</head>

<body class="bg-light">

    <div class="container py-5">

        <h1 class="mb-3">

            Sistema de Categorización de consultas automático

        </h1>

        <p class="lead-text mb-5">
            Enviarnos cualquier consulta. Será clasificada automáticamente por un modelo de IA y enviada al departamento
            correspondiente.
        </p>

        @if (session('success'))
            <div class="alert alert-success" role="alert">
                {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger" role="alert">
                {{ session('error') }}
            </div>
        @endif

        <div class="card shadow-sm mb-5">

            <div class="card-body">

                <form method="POST" action="/consultas">

                    @csrf

                    <div class="mb-3">

                        <label class="form-label" for="customer_message">

                            Consulta

                        </label>
                        <div class="alert alert-light border mb-4">

                            <p class="mb-2 mt-2">
                                El sistema analiza el contenido de la consulta y puede asignarle una o varias
                                categorías.
                            </p>

                            <div class="d-flex flex-wrap gap-2">
                                <span>[ TECH_SUPPORT | COMMERCIAL | ACKNOWLEDGMENT | OTHER ] </span>
                            </div>
                        </div>

                        <textarea class="form-control @error('customer_message') is-invalid @enderror" rows="5"
                            maxlength="500" minlength="10" id="customer_message" name="customer_message"
                            required>{{ old('customer_message') }}</textarea>

                        @error('customer_message')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror

                        <div class="form-text">

                            Entre 10 y 500 caracteres

                        </div>

                    </div>

                    <button id="submitButton" disabled class="btn btn-primary">
                        Enviar consulta
                    </button>

                </form>

            </div>

        </div>

        <div class="card shadow-sm">

            <h5 class="mb-0">
                Historial de consultas
            </h5>

            <div class="card-body p-0">

                <table class="table table-hover align-middle mb-0">

                    <thead class="table-light">

                        <tr>

                            <th>ID</th>

                            <th>Consulta</th>

                            <th>Categoría</th>

                            <th>Proveedor</th>

                            <th>Modelo</th>

                            <th>Prompt</th>

                            <th>Status</th>

                            <th>Creada</th>

                        </tr>

                    </thead>

                    <tbody>

                        @foreach($consultas as $consulta)

                            <tr>

                                <td>

                                    <span class="fw-semibold text-secondary">

                                        #{{ $consulta->id }}

                                    </span>

                                </td>

                                <td class="message-cell">

                                    {{ $consulta->customer_message }}

                                </td>

                                <td>

                                    @foreach($consulta->categories as $category)

                                        @php

                                            $class = match ($category->name) {

                                                'ACKNOWLEDGMENT'
                                                => 'badge-category'
                                                    . ' text-success'
                                                    . ' border border-success-subtle'
                                                    . ' bg-success-subtle',

                                                'TECH_SUPPORT'
                                                => 'badge-category'
                                                    . ' text-primary'
                                                    . ' border border-primary-subtle'
                                                    . ' bg-primary-subtle',

                                                'COMMERCIAL'
                                                => 'badge-category'
                                                    . ' text-warning-emphasis'
                                                    . ' border border-warning-subtle'
                                                    . ' bg-warning-subtle',

                                                'OTHER'
                                                => 'badge-category'
                                                    . ' text-secondary'
                                                    . ' border border-secondary-subtle'
                                                    . ' bg-secondary-subtle',

                                                default
                                                => 'badge-category bg-light text-dark'

                                            };

                                        @endphp


                                        <span class="badge {{ $class }}">
                                            {{ $category->name }}
                                        </span>

                                    @endforeach

                                </td>

                                <td>

                                    <span class="badge text-bg-light border">

                                        {{ strtoupper($consulta->llmConfig->provider) }}

                                    </span>

                                </td>

                                <td>

                                    <code>

                                                    {{ $consulta->llmConfig->model }}

                                                </code>

                                </td>

                                <td>

                                    <span class="badge rounded-pill text-bg-dark">

                                        {{ strtoupper($consulta->promptVersion->version) }}

                                    </span>
                                </td>

                                @php

                                    $statusClass = match ($consulta->status->value) {

                                        'SUCCESS' => 'status-success',

                                        'ERROR' => 'status-error',

                                        'TIMEOUT' => 'status-timeout',

                                        'NO_CREDITS' => 'status-no-credits',

                                        default => 'status-error',

                                    };

                                @endphp

                                <td>


                                    <span class="{{ $statusClass }}">
                                        {{ $consulta->status->value }}
                                    </span>
                                </td>

                                <td>

                                    {{ $consulta->created_at->format('d/m/Y H:i') }}

                                </td>

                            </tr>

                        @endforeach

                    </tbody>

                </table>

            </div>

        </div>

    </div>

    <script>
        const form = document.querySelector('form');
        const textarea = document.getElementById('customer_message');
        const button = document.getElementById('submitButton');

        const syncSubmitButton = () => {
            button.disabled = textarea.value.trim().length < 10;
        };

        textarea.addEventListener('input', syncSubmitButton);

        form.addEventListener('submit', () => {
            button.disabled = true;
            button.innerText = 'Enviando consulta...';
        });

        syncSubmitButton();
    </script>

</body>

</html>