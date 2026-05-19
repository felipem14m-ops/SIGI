# Editar Proveedor

La lógica de esta funcionalidad se maneja principalmente desde la vista auth/Adminproveedores.php.

Para realizar esta acción, el usuario interactúa con el botón de editar que se encuentra en cada fila de la tabla de proveedores. El javascript de la página se encarga de capturar el ID del proveedor seleccionado, buscar sus datos en el array de proveedores que está cargado en formato JSON, y prellenar todos los campos del modal de edición con la información actual del proveedor incluyendo nombre, teléfono, email y empresa.

Al momento de enviar los datos modificados, el formulario en su atributo action está conectado directamente con el archivo ProveedorController.php con el parámetro accion=editar. El formulario incluye un campo oculto con el ID del proveedor. Dentro de este controlador, el sistema pasa por la acción o función llamada editar en función principal.

Cabe recordar que este controlador incluye todos los archivos de configuración como database.php para tener permisos en la base de datos, y se apoya en el modelo Proveedor.php necesario para interactuar con la tabla proveedores. Una vez que el controlador recibe los datos, primero valida que el ID del proveedor sea un número entero válido mayor a cero. Luego valida que el nombre no esté vacío. Los campos opcionales de teléfono, email y empresa se aceptan tal como vienen, pudiendo ser cadenas vacías que se convertirán en NULL. Si se proporciona un email, se valida su formato. Después de todas estas validaciones, el controlador llama al método actualizar del modelo pasándole el ID y todos los datos modificados.

El modelo Proveedor.php recibe el ID y los datos, y prepara una consulta SQL de tipo UPDATE con los campos nombre, telefono, email y empresa usando consultas preparadas con PDO. La consulta actualiza el proveedor en la tabla proveedores modificando solo los campos enviados. El campo fecha_registro no se modifica ya que representa cuándo se creó originalmente el proveedor. El modelo maneja cualquier error de base de datos y retorna true si fue exitoso o un mensaje de error si falló.

Al finalizar el proceso con éxito o si ocurre algún error, el mismo controlador se encarga de hacer el redireccionamiento para devolver al usuario a la vista auth/Adminproveedores.php, mostrando un mensaje flotante con SweetAlert2 indicando si el proveedor fue actualizado correctamente o si hubo algún error en el proceso. Los productos que ya tenían asignado este proveedor mantienen la relación y verán reflejados los cambios en el nombre o información del proveedor.
