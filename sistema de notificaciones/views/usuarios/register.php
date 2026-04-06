<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title> Registro de Acudientes</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="bg-slate-50 min-h-screen flex items-center justify-center py-12 px-4">

    <div class="max-w-2xl w-full bg-white rounded-3xl shadow-xl overflow-hidden border border-gray-100">
        <div class="bg-blue-700 p-8 text-white text-center">
            <div class="text-3xl mb-2"><i class="fas fa-user-plus"></i></div>
            <h2 class="text-2xl font-bold">Registro de Acudiente</h2>
            <p class="text-blue-100 text-sm">Crea tu cuenta para realizar el seguimiento académico</p>
        </div>

        <form action="procesar_registro.php" method="POST" class="p-8 md:p-12">
            
            <input type="hidden" name="rol" value="acudiente">
            <input type="hidden" name="activo" value="1">

            <div class="grid md:grid-cols-2 gap-6">
                <div class="space-y-1">
                    <label class="text-sm font-semibold text-gray-600">Nombres</label>
                    <div class="relative">
                        <input type="text" name="nombres" required maxlength="100"
                            class="w-full pl-4 pr-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:bg-white outline-none transition"
                            placeholder="Ej. Juan Carlos">
                    </div>
                </div>

                <div class="space-y-1">
                    <label class="text-sm font-semibold text-gray-600">Apellidos</label>
                    <div class="relative">
                        <input type="text" name="apellidos" required maxlength="100"
                            class="w-full pl-4 pr-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:bg-white outline-none transition"
                            placeholder="Ej. Pérez Rodríguez">
                    </div>
                </div>
            </div>

            <div class="mt-6 space-y-1">
                <label class="text-sm font-semibold text-gray-600">Correo Electrónico</label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400">
                        <i class="fas fa-envelope"></i>
                    </span>
                    <input type="email" name="email" required maxlength="150"
                        class="w-full pl-10 pr-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:bg-white outline-none transition"
                        placeholder="correo@ejemplo.com">
                </div>
            </div>

            <div class="grid md:grid-cols-2 gap-6 mt-6">
                <div class="space-y-1">
                    <label class="text-sm font-semibold text-gray-600">Contraseña</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400">
                            <i class="fas fa-lock"></i>
                        </span>
                        <input type="password" name="password" required
                            class="w-full pl-10 pr-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:bg-white outline-none transition"
                            placeholder="••••••••">
                    </div>
                </div>

                <div class="space-y-1">
                    <label class="text-sm font-semibold text-gray-600">Confirmar Contraseña</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400">
                            <i class="fas fa-check-double"></i>
                        </span>
                        <input type="password" name="confirm_password" required
                            class="w-full pl-10 pr-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:bg-white outline-none transition"
                            placeholder="••••••••">
                    </div>
                </div>
            </div>

            <div class="mt-8 flex items-start">
                <div class="flex items-center h-5">
                    <input id="terms" type="checkbox" required class="w-4 h-4 border border-gray-300 rounded bg-gray-50 focus:ring-3 focus:ring-blue-300">
                </div>
                <label for="terms" class="ml-3 text-sm text-gray-500 leading-tight">
                    Acepto los términos de servicio y la política de tratamiento de datos académicos.
                </label>
            </div>

            <div class="mt-8">
                <button type="submit" 
                    class="w-full bg-blue-700 text-white font-bold py-4 rounded-xl hover:bg-blue-800 shadow-lg shadow-blue-200 transform hover:-translate-y-1 transition-all">
                    Crear Cuenta de Acudiente
                </button>
            </div>

            <div class="mt-6 text-center">
                <p class="text-sm text-gray-500">
                    ¿Ya tienes una cuenta? <a href="login.php" class="text-blue-700 font-bold hover:underline">Inicia sesión aquí</a>
                </p>
            </div>
        </form>
    </div>

</body>
</html>