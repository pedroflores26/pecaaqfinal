document.addEventListener("DOMContentLoaded", () => {

  // ==================== CARREGAR PERFIL ====================
  async function carregarPerfil() {
    try {
      const resp = await fetch("perfil_empresa.php", { cache: "no-store" });
      const data = await resp.json();

      if (!data.erro) {
        const nome = data.nome_razao_social || "---";
        const cnpj = data.documento || "---";
        const email = data.email || "---";
        const telefone = data.telefone || "---";

        document.getElementById("perfilNomeEmpresa").textContent = nome;
        document.getElementById("perfilCNPJ").textContent = "CNPJ: " + cnpj;
        document.getElementById("perfilEmail").textContent = email;
        document.getElementById("perfilTelefone").textContent = telefone;
        document.getElementById("headerEmpresaNome").textContent = nome;

        // Salva no localStorage para edição posterior
        const usuario = {
          id_usuario: data.id_usuario,
          nome_razao_social: nome,
          email: email,
          telefone: telefone
        };
        localStorage.setItem("usuarioLogado", JSON.stringify(usuario));
      } else {
        console.warn("Erro ao carregar perfil:", data.msg);
      }
    } catch (err) {
      console.error("Falha ao carregar perfil:", err);
    }
  }

  carregarPerfil();

  // ==================== EDITAR PERFIL ====================
  const btnEditarPerfil = document.getElementById("btnEditarPerfil");
  const modalEditarPerfil = document.getElementById("modalEditarPerfil");
  const btnFecharEditarPerfil = document.getElementById("btnFecharEditarPerfil");
  const formEditarPerfil = document.getElementById("formEditarPerfil");
  const msgEditarPerfil = document.getElementById("msgEditarPerfil");
  const inputEmail = document.getElementById("editarEmail");
  const inputTelefone = document.getElementById("editarTelefone");

  if (btnEditarPerfil) {
    btnEditarPerfil.addEventListener("click", async () => {
      const usuario = JSON.parse(localStorage.getItem("usuarioLogado"));
      if (!usuario) return alert("Nenhum usuário logado!");

      inputEmail.value = usuario.email || "";
      inputTelefone.value = usuario.telefone || "";
      msgEditarPerfil.textContent = "";

      modalEditarPerfil.style.display = "block";
    });
  }

  if (btnFecharEditarPerfil) {
    btnFecharEditarPerfil.addEventListener("click", () => {
      modalEditarPerfil.style.display = "none";
      formEditarPerfil.reset();
    });
  }

  if (formEditarPerfil) {
    formEditarPerfil.addEventListener("submit", async (e) => {
      e.preventDefault();

      const usuario = JSON.parse(localStorage.getItem("usuarioLogado"));
      if (!usuario) return alert("Nenhum usuário logado!");

      const email = inputEmail.value.trim();
      const telefone = inputTelefone.value.trim();
      const id_usuario = usuario.id_usuario;

      if (!email || !telefone) {
        return alert("Preencha email e telefone corretamente.");
      }

      const formData = new FormData();
      formData.append("id_usuario", id_usuario);
      formData.append("email", email);
      formData.append("telefone", telefone);

      try {
        const resp = await fetch("atualizarPerfil.php", {
          method: "POST",
          body: formData
        });
        const data = await resp.json();

        if (data.ok) {
          alert("Perfil atualizado com sucesso!");
          document.getElementById("perfilEmail").textContent = email;
          document.getElementById("perfilTelefone").textContent = telefone;

          usuario.email = email;
          usuario.telefone = telefone;
          localStorage.setItem("usuarioLogado", JSON.stringify(usuario));

          modalEditarPerfil.style.display = "none";
        } else {
          alert(data.msg || "Erro ao atualizar perfil.");
        }
      } catch (err) {
        console.error(err);
        alert("Erro de conexão. Tente novamente.");
      }
    });
  }

  // ==================== LOGOUT ====================
  const btnSair = document.getElementById("btnSair");
  const btnSidebarSair = document.getElementById("btnSidebarSair");
  const btnSairEmpresa = document.getElementById("btnSairEmpresa");

  // ✅ Caminho fixo para o login (sem erro de porta)
  const URL_LOGIN = "../login/indexLogin.html";

  function sair() {
    localStorage.removeItem("usuarioLogado");
    sessionStorage.clear();
    window.location.href = URL_LOGIN;
  }

  if (btnSair) btnSair.addEventListener("click", sair);
  if (btnSidebarSair) btnSidebarSair.addEventListener("click", sair);
  if (btnSairEmpresa) btnSairEmpresa.addEventListener("click", sair);

});
