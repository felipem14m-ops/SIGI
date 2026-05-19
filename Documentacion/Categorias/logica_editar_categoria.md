# Editar Categoría

La lógica de esta funcionalidad se maneja principalmente desde la vista auth/Admincategorias.php.

Para realizar esta acción, el usuario interactúa con el botón de editar que se encuentra en cada fila de la tabla de categorías. El javascript de la página se encarga de capturar el ID de la categoría seleccionada, buscar sus datos en el array de categorías que está cargado en formato JSON, y prellenar todos los campos del modal de edición con la información actual de la categoría incluyendo nombre, descripción y si tiene imagen muestra una vista previa de la misma.

Al momento de enviar los datos modificados, el formulario en su atributo action está conectado directamente con el archivo CategoriaController.php con el parámetro accion=editar. El formulario incluye un campo oculto con el ID de la categoría y otro campo oculto con el nombre de la imagen actual. Dentro de este controlador, el sistema pasa por la acción o función llamada editar en función principal.

Cabe recordar que este controlador incluye todos los archivos de configuración como database.php para tener permisos en la base de datos, y se apoya en el modelo Categoria.php necesario para interactuar con la tabla categorias. Una vez que el controlador recibe los datos, primero valida que el ID de la categoría sea un número entero válido mayor a cero. Luego valida que el nombre no esté vacío. Si el usuario subió una nueva imagen, el controlador valida el formato y tamaño, genera un nombre único, elimina la imagen anterior del servidor si existía, y mueve la nueva imagen a la carpeta IMG/categorias. Si no se subió nueva imagen, mantiene el nombre de la imagen actual que viene del campo oculto. Después de todas estas validaciones, el controlador llama al método actualizar del modelo pasándole el ID y todos los datos modificados.

El modelo Categoria.php recibe el ID y los datos, y prepara una consulta SQL de tipo UPDATE con los campos nombre, descripcion e imagen usando consultas preparadas con PDO. La consulta actualiza la categoría en la tabla categorias modificando solo los campos enviados. Si el nombre modificado ya existe en otra categoría, MySQL retorna un error por el constraint UNIQUE y el modelo lo captura para mostrar un mensaje apropiado.

Al finalizar el proceso con éxito o si ocurre algún error, el mismo controlador se encarga de hacer el redireccionamiento para devolver al usuario a la vista auth/Admincategorias.php, mostrando un mensaje flotante con SweetAlert2 indicando si la categoría fue actualizada correctamente o si hubo algún error en el proceso.
