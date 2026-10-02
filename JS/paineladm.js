const formulario = document.querySelector("form");

formulario.addEventListener("submit", function(event) {
    event.preventDefault();

    const email = document.querySelector("#email").value;
    const senha = document.querySelector("#senha").value;

    const emailCorreto = "admin@gsnews.com";
    const senhaCorreta = "admin123";

    if (email === emailCorreto && senha === senhaCorreta) {
        window.location.href = "dashboard.html";
    } else {
        alert("E-mail ou senha incorretos.");
    }
});
