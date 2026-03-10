function abrirModalIngresos(codigo) {
  var modal = document.getElementById("modalIngresos");
  var contenido = document.getElementById("modalContenidoIngresos");
  modal.className = modal.className.replace("hidden", "flex");
  contenido.className = contenido.className.replace(
    "scale-95 opacity-0",
    "scale-100 opacity-100"
  );
  var inputCodigo = document
    .getElementById("contenidoModalIngresos")
    .getElementsByTagName("input")[0];
  if (inputCodigo) inputCodigo.value = codigo;
  var xhr = new XMLHttpRequest();
  xhr.open(
    "GET",
    "buscar_ingresos_usuario.php?codigo=" + encodeURIComponent(codigo),
    true
  );
  xhr.onreadystatechange = function () {
    if (xhr.readyState == 4) {
      if (xhr.status == 200) {
        document.getElementById("resultadosIngresos").innerHTML =
          xhr.responseText;
      } else {
        document.getElementById("resultadosIngresos").innerHTML =
          '<tr><td colspan="5" class="text-center text-red-600">Error cargando resultados.</td></tr>';
      }
    }
  };
  xhr.send();
}
function cerrarModalIngresos() {
  var modal = document.getElementById("modalIngresos");
  var contenido = document.getElementById("modalContenidoIngresos");

  contenido.className = contenido.className.replace(
    "scale-100 opacity-100",
    "scale-95 opacity-0"
  );
  setTimeout(function () {
    modal.className = modal.className.replace("flex", "hidden");
    document.getElementById("contenidoModalIngresos").innerHTML =
      '<p class="text-center text-gray-500">Cargando ingresos...</p>';
  }, 300);
}
function buscarIngresos() {
  const form = document.getElementById("formIngresos");
  const datos = new URLSearchParams(new FormData(form)).toString();
  fetch("buscar_ingresos_usuario.php?" + datos)
    .then((res) => res.text())
    .then((html) => {
      document.getElementById("resultadosIngresos").innerHTML = html;
    })
    .catch((err) => {
      document.getElementById("resultadosIngresos").innerHTML =
        '<tr><td colspan="5" class="text-center text-red-600">Error cargando resultados.</td></tr>';
      console.error(err);
    });
}
