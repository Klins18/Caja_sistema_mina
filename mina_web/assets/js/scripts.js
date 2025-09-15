document.addEventListener("DOMContentLoaded", () => {
    const proveedorInput = document.getElementById("proveedor");

    proveedorInput.addEventListener("input", async () => {
        const query = proveedorInput.value;
        if (query.length > 1) {
            const response = await fetch(`search_proveedores.php?q=${query}`);
            const data = await response.json();

            let datalist = document.getElementById("proveedoresList");
            if (!datalist) {
                datalist = document.createElement("datalist");
                datalist.id = "proveedoresList";
                document.body.appendChild(datalist);
                proveedorInput.setAttribute("list", "proveedoresList");
            }
            datalist.innerHTML = "";
            data.forEach(item => {
                const option = document.createElement("option");
                option.value = item.nombre;
                datalist.appendChild(option);
            });
        }
    });
});
document.addEventListener("DOMContentLoaded", () => {
    const familiaInput = document.getElementById("familia");

    familiaInput.addEventListener("input", async () => {
        const query = familiaInput.value;
        if (query.length > 1) {
            const response = await fetch(`search_familia.php?q=${query}`);
            const data = await response.json();

            let datalist = document.getElementById("familiasList");
            if (!datalist) {
                datalist = document.createElement("datalist");
                datalist.id = "familiasList";
                document.body.appendChild(datalist);
                familiaInput.setAttribute("list", "familiasList");
            }
            datalist.innerHTML = "";
            data.forEach(item => {
                const option = document.createElement("option");
                option.value = item.nombre;
                datalist.appendChild(option);
            });
        }
    });
});
