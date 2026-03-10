function abrirModalBusquedaParametrica() {
  const modal = document.getElementById("modalBusquedaParametrica");
  const contenido = document.getElementById("modalContenidoBusqueda");

  modal.classList.remove("hidden");
  modal.classList.add("flex");

  contenido.classList.remove("scale-95", "opacity-0");
  contenido.classList.add("scale-100", "opacity-100");

  fetch("buscar_usuario.php")
    .then((response) => response.text())
    .then((html) => {
      document.getElementById("contenidoModalBusquedaParametrica").innerHTML =
        html;
      inicializarBusquedaUnidades(); // Llamar después de insertar el contenido
    })
    .catch((err) => {
      document.getElementById("contenidoModalBusquedaParametrica").innerHTML =
        '<p class="text-red-600 text-center">Error cargando el formulario.</p>';
      console.error(err);
    });
}

const unidadIds = [];

function actualizarLista() {
  const lista = document.getElementById("lista-unidades");
  const hidden = document.getElementById("unidades-ids");
  lista.innerHTML = "";

  unidadIds.forEach((id) => {
    const option = document.querySelector(
      `#unidad-select option[value="${CSS.escape(id)}"]`
    );
    if (!option) return;

    // Crear chip visual
    const chip = document.createElement("div");
    chip.className =
      "flex items-center justify-between bg-gray-100 border rounded px-2 py-1";

    const span = document.createElement("span");
    span.textContent = option.text;

    // Botón X
    const btnX = document.createElement("button");
    btnX.type = "button";
    btnX.textContent = "✖";
    btnX.className = "text-red-500 hover:text-red-700 ml-2";
    btnX.onclick = () => {
      const idx = unidadIds.indexOf(id);
      if (idx !== -1) {
        unidadIds.splice(idx, 1);
        actualizarLista();
      }
    };

    chip.appendChild(span);
    chip.appendChild(btnX);
    lista.appendChild(chip);
  });

  // Actualizar hidden
  hidden.value = unidadIds.join(",");
}

function agregarUnidad(e) {
  if (e) e.preventDefault();
  const select = document.getElementById("unidad-select");
  const selected = select.options[select.selectedIndex];
  if (!selected) return;

  const id = String(selected.value);
  if (!unidadIds.includes(id)) {
    unidadIds.push(id);
    actualizarLista();
  }
}

function inicializarBusquedaUnidades() {
  const select = document.getElementById("unidad-select");
  const buscar = document.getElementById("unidad-buscar");
  const btnAgregar = document.getElementById("btn-agregar");

  if (buscar && select) {
    buscar.addEventListener("input", function () {
      const filtro = this.value.toLowerCase();
      Array.from(select.options).forEach((opt) => {
        opt.style.display = opt.text.toLowerCase().includes(filtro)
          ? ""
          : "none";
      });
    });
  }

  if (btnAgregar) {
    btnAgregar.addEventListener("click", agregarUnidad);
  }
}

document.addEventListener("DOMContentLoaded", inicializarBusquedaUnidades);

//FUNCIÓN PARA BUSQUEDA PARAMÉTRICA
function buscarParametros() {
  var apellido1 = document.getElementsByName("apellido1")[0].value.trim();
  var apellido2 = document.getElementsByName("apellido2")[0].value.trim();
  var nombre1 = document.getElementsByName("nombre1")[0].value.trim();
  var nombre2 = document.getElementsByName("nombre2")[0].value.trim();

  var unidades = document.getElementById("unidades-ids").value;

  var perfilesSeleccionados = [];
  var checkboxes = document.getElementsByName("perfiles[]");
  for (var i = 0; i < checkboxes.length; i++) {
    if (checkboxes[i].checked) {
      perfilesSeleccionados.push(checkboxes[i].value);
    }
  }

  var url =
    "gestor_usuarios.php?" +
    "apellido1=" +
    encodeURIComponent(apellido1) +
    "&apellido2=" +
    encodeURIComponent(apellido2) +
    "&nombre1=" +
    encodeURIComponent(nombre1) +
    "&nombre2=" +
    encodeURIComponent(nombre2) +
    "&unidades=" +
    encodeURIComponent(unidades) +
    "&perfiles=" +
    encodeURIComponent(perfilesSeleccionados.join(","));

  window.location.href = url;
}

function cerrarModal() {
  const modal = document.getElementById("modalBusquedaParametrica");
  const contenido = document.getElementById("modalContenidoBusqueda");

  contenido.classList.remove("scale-100", "opacity-100");
  contenido.classList.add("scale-95", "opacity-0");

  setTimeout(() => {
    modal.classList.remove("flex");
    modal.classList.add("hidden");
    document.getElementById("contenidoModalBusquedaParametrica").innerHTML =
      '<p class="text-center text-gray-500">Cargando...</p>';
  }, 300);
  location.reload();
}
