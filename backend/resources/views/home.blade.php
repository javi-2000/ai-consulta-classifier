<!doctype html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Ecig Logística</title>
    <link rel="icon" href="{{ asset('favicon.ico') }}" type="image/x-icon">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">

    <div class="container py-5">

        <h1 class="mb-3">

            Sistema de Categorización de consultas automático

        </h1>

        <p class="text-muted mb-4">

            A continuación puede enviarnos cualquier consulta. Será clasificada y enviada a nuestro equipo de soporte.

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
                        Enviar
                    </button>

                </form>

            </div>

        </div>

        <div class="card shadow-sm">

            <div class="card-header">

                Consultas

            </div>

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

                                    {{ $consulta->id }}

                                </td>

                                <td style="max-width:350px">

                                    {{ $consulta->customer_message }}

                                </td>

                                <td>

                                    @foreach($consulta->categories as $category)

                                        @php
                                            $class = match ($category->name) {
                                                'ACKNOWLEDGMENT' => 'bg-success',
                                                'TECH_SUPPORT' => 'bg-primary',
                                                'COMMERCIAL' => 'bg-warning text-dark',
                                                'OTHER' => 'bg-info text-dark',
                                                default => 'bg-secondary'
                                            };
                                        @endphp


                                        <span class="badge {{ $class }}">
                                            {{ $category->name }}
                                        </span>

                                    @endforeach

                                </td>

                                <td>

                                    {{ $consulta->llmConfig->provider }}

                                </td>

                                <td>

                                    {{ $consulta->llmConfig->model }}

                                </td>

                                <td>

                                    {{ $consulta->promptVersion->version }}

                                </td>

                                <td>

                                    {{ $consulta->status->value }}

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
        const loading = document.getElementById('loading');

        const syncSubmitButton = () => {
            button.disabled = textarea.value.trim().length < 10;
        };

        textarea.addEventListener('input', syncSubmitButton);

        form.addEventListener('submit', () => {
            button.disabled = true;
            button.innerText = 'Enviando consulta...';
            loading.style.display = 'block';
        });

        syncSubmitButton();
    </script>

</body>

</html>