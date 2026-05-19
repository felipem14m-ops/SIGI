# Logout

La lógica de esta funcionalidad se maneja principalmente desde el controlador AuthController.php.

Para realizar esta acción, el usuario interactúa con el botón o enlace de cerrar sesión que generalmente se encuentra en el menú superior o lateral del dashboard. Este botón está configurado para redirigir al archivo AuthController.php con el parámetro accion=logout en la URL. No se requiere un formulario ni datos adicionales, simplemente es una petición GET que indica la intención del usuario de terminar su sesión actual.

Al momento de recibir la petición, el controlador identifica la acción logout en su enrutador principal y ejecuta el método logout de la clase AuthController. Este método es muy directo y se enfoca en la seguridad de la sesión. Primero ejecuta session_unset que elimina todas las variables de sesión registradas, borrando datos como logged_in, usuario con su id, nombre, email y rol, y cualquier otra información temporal almacenada durante la navegación del usuario por el sistema.

Después de limpiar las variables, el controlador ejecuta session_destroy que destruye completamente la sesión del lado del servidor, eliminando el archivo de sesión almacenado en el servidor y liberando los recursos asociados. Esto asegura que no queden rastros de la sesión anterior y previene que alguien pueda reutilizar la sesión si tiene acceso físico a la computadora. Es importante mencionar que session_destroy no elimina la cookie de sesión del navegador inmediatamente, pero al no existir el archivo de sesión en el servidor, la cookie se vuelve inútil.

Al finalizar el proceso de limpieza, el controlador redirige automáticamente al usuario a la página de login ubicada en views/Usuario/login.php usando header con Location. Esta redirección es inmediata y no muestra ningún mensaje de confirmación, simplemente lleva al usuario de vuelta a la pantalla de inicio de sesión donde puede volver a autenticarse si lo desea. El uso de exit después del header asegura que no se ejecute ningún código adicional después de la redirección.

Si el usuario intenta acceder a cualquier página protegida del sistema después de hacer logout, las validaciones de sesión al inicio de cada vista detectarán que no existe la variable logged_in o que su valor no es true, y automáticamente redirigirán al usuario de vuelta al login. Esto previene el acceso no autorizado incluso si el usuario intenta usar el botón de retroceso del navegador o tiene URLs guardadas en el historial.

Es importante destacar que el logout no requiere confirmación del usuario ni validación de contraseña, ya que cerrar sesión es una acción que no compromete la seguridad del sistema. Sin embargo, algunas implementaciones podrían agregar un diálogo de confirmación con JavaScript del lado del cliente para prevenir cierres de sesión accidentales, pero esto es opcional y no afecta la funcionalidad del servidor.
