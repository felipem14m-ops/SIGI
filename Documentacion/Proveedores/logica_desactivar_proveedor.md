# Desactivar Proveedor

La lógica de esta funcionalidad se maneja principalmente desde la vista auth/Adminproveedores.php.

Para realizar esta acción, el usuario interactúa con el botón de desactivar que se encuentra en cada fila de la tabla de proveedores, específicamente en los proveedores que tienen estado activo. El javascript de la página se encarga de mostrar un diálogo de confirmación con SweetAlert2 preguntando al usuario si está seguro de desactivar el proveedor, mostrando el nombre del proveedor y explicando que será ocultado del sistema pero que puede reactivarse cuando sea necesario.

Al momento de confirmar la acción, el sistema redirige directamente al archivo ProveedorController.php con los parámetros accion=desactivar e id con el número del proveedor. Dentro de este controlador, el sistema pasa por la acción o función llamada desactivar en función principal.

Cabe recordar que este controlador incluye todos los archivos de configuración como database.php para tener permisos en la base de datos, y se apoya en el modelo Proveedor.php necesario para interactuar con la tabla proveedores. Una vez que el controlador recibe la petición, valida que el ID del proveedor sea un número entero válido mayor a cero. A diferencia de las categorías, los proveedores pueden desactivarse aunque tengan productos asignados, ya que la relación es opcional y los productos pueden existir sin proveedor. El controlador procede a llamar al método cambiarEstado del modelo pasándole el ID del proveedor y el nuevo estado que será 0 para inactivo.

El modelo Proveedor.php recibe el ID y el estado, y prepara una consulta SQL de tipo UPDATE muy simple que solo modifica el campo activo del proveedor usando consultas preparadas con PDO. La consulta ejecuta UPDATE proveedores SET activo igual a 0 WHERE id_proveedor igual al ID recibido. Esta operación es muy rápida y segura porque no elimina ningún dato, solo cambia el estado.

Los proveedores inactivos no aparecen en los selects de formularios cuando se crea o edita un producto, pero sí aparecen en la tabla de administración de proveedores con un badge gris para que el administrador pueda gestionarlos. Los productos que ya tenían asignado ese proveedor mantienen la relación y siguen mostrando el nombre del proveedor en sus detalles, pero no se pueden asignar nuevos productos a un proveedor inactivo. Esto es útil cuando un proveedor deja de trabajar con la empresa pero se quiere mantener el historial de qué productos venían de ese proveedor.

Al finalizar el proceso con éxito o si ocurre algún error, el mismo controlador se encarga de hacer el redireccionamiento para devolver al usuario a la vista auth/Adminproveedores.php, mostrando un mensaje flotante con SweetAlert2 indicando si el proveedor fue desactivado correctamente o si hubo algún error en el proceso. Si el usuario desea reactivar el proveedor, el proceso es exactamente el mismo pero cambiando el estado de 0 a 1.
