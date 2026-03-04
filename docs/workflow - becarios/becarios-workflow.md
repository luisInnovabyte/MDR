# Workflow de Becarios — Proyecto AssetTrack (MDR ERP)

> **Documento vivo** — Sujeto a revisión y actualización continua.  
> Última actualización: 2026-02-27

---

## 1. Acceso al repositorio GitHub

- El propietario del repositorio invita a cada becario desde:  
  `GitHub → Repositorio AssetTrack → Settings → Collaborators → Add people`
- Los becarios recibirán una invitación por email que deben aceptar antes de poder trabajar.
- En repositorios públicos con plan gratuito el rol **Write** se asigna automáticamente sin necesidad de seleccionarlo.

### Protección de la rama main

Antes de dar acceso, configurar en `Settings → Branches → Add ruleset` sobre `main`:

1. Activar **Require a pull request before merging**
2. Activar **Require approvals** (1 aprobación — el responsable del proyecto)
3. Activar **Restrict updates** (solo el responsable puede actualizar `main`)
4. Dejar activadas **Restrict deletions** y **Block force pushes** (vienen marcadas por defecto, son beneficiosas)
5. En la sección **Target branches** → pulsar **Add target** → **Include by pattern** → escribir `main`
6. Guardar el ruleset

> **Nota:** En repositorios privados con plan gratuito de GitHub los rulesets no se aplican. Se requiere plan **GitHub Team** (4$/usuario/mes). En repositorios públicos funcionan sin restricciones.

---

## 2. Clonado del proyecto en la máquina virtual Proxmox

### Paso 1 — Instalar Git en la VM (solo la primera vez, desde terminal)
```bash
sudo apt install git -y
```

### Paso 2 — Identificarse en Git (una vez por VM)
```bash
git config --global user.name "Nombre Becario"
git config --global user.email "email@becario.com"
```

### Paso 3 — Clonar desde VS Code
1. `Ctrl+Shift+P` → `Git: Clone`
2. Pegar la URL HTTPS del repositorio de GitHub
3. Seleccionar carpeta de destino en la VM
4. Si VS Code no tiene sesión de GitHub guardada pedirá autenticación → introducir usuario y **Personal Access Token**  
   *(GitHub → Settings → Developer settings → Personal access tokens → Tokens classic → permisos `repo`)*
5. VS Code preguntará **¿Desea abrir el repositorio?** → pulsar **Abrir**
6. El proyecto quedará abierto en VS Code

---

## 3. Configuración del entorno local (.env)

- El archivo `.env` **no está en GitHub** y debe proporcionarlo el responsable del proyecto.
- Contiene las credenciales de acceso al VPS de base de datos compartida.
- El usuario MySQL de los becarios tiene permisos **solo de lectura/escritura** sobre los datos.  
  **Sin permisos de CREATE TABLE ni ejecución de scripts DDL.**
- Cada becario copia el `.env` en la raíz del proyecto en su VM.

---

## 4. Estructura de ramas de trabajo

Cada becario trabaja en su propia rama permanente:

| Becario | Rama |
|---------|------|
| Becario 1 | `dev-becario1` |
| Becario 2 | `dev-becario2` |

### Creación de la rama desde VS Code
1. Clic en **main** en la barra inferior izquierda de VS Code
2. Seleccionar **Create new branch**
3. Introducir el nombre de la rama (`dev-becario1` o `dev-becario2`)
4. Pulsar Enter
5. En el panel **Control de código fuente** pulsar **Publish Branch** para publicarla en GitHub

---

## 5. Operativa diaria en VS Code

### Al comenzar la jornada
1. Abrir VS Code con el proyecto
2. Verificar que se está en la rama propia (barra inferior izquierda)
3. Sincronizar cambios: panel **Control de código fuente** → icono de sincronización  
   o `Ctrl+Shift+P` → `Git: Pull`

### Durante el desarrollo
Cuando se completa una unidad funcional coherente:

1. Panel **Control de código fuente** → revisar archivos modificados
2. Clic en `+` junto a los archivos para pasarlos a **Staged** (o `+` general para todos)
3. Escribir el mensaje de commit con formato descriptivo:  
   `feat: añadida validación en formulario de clientes`
4. Pulsar **Commit** (`Ctrl+Enter`)
5. Pulsar **Sync Changes** / **Push** para subir a GitHub

### Al finalizar la jornada
- Todos los cambios deben estar commiteados y subidos a GitHub.
- **Nunca cerrar la jornada con trabajo sin commitear.**

### Cuando una funcionalidad está terminada
El becario crea un **Pull Request** desde GitHub:

1. Ir al repositorio en GitHub
2. Si GitHub muestra el aviso **Compare & pull request** pulsarlo directamente
3. Si no aparece, ir a `https://github.com/luisInnovabyte/AssetTrack/compare`
4. Seleccionar **base:** `main` y **compare:** rama del becario
5. Rellenar:
   - **Title:** descripción breve de la funcionalidad
   - **Description:** detalle de lo implementado
   - **Reviewers:** asignar al responsable del proyecto
6. Pulsar **Create pull request**

El responsable revisa en la pestaña **Files changed**, solicita correcciones si procede, aprueba y realiza el merge a `main` con **Merge pull request → Confirm merge**.

### Cómo probar el código del becario antes de aprobar el merge

Para probar la aplicación con el código del becario sin tocar `main`:

1. En VS Code clic en el nombre de la rama activa en la barra inferior izquierda
2. En el desplegable aparecen dos secciones: **ramas** (locales) y **ramas remotas**
3. En **ramas remotas** seleccionar `origin/dev-becario1` o `origin/dev-becario2`
4. VS Code creará automáticamente una copia local y cambiará a esa rama
5. Probar la aplicación con normalidad en el entorno local
6. Si todo es correcto → volver a GitHub y aprobar el merge
7. Si hay correcciones → añadirlas como comentarios en el PR para que el becario las resuelva
8. Para volver a tu rama → clic en la barra inferior izquierda → seleccionar **`main`**

> **Importante:** En ningún momento `main` se ve afectada durante este proceso.

---

## 6. Extensiones recomendadas en VS Code

| Extensión | Uso |
|-----------|-----|
| **GitLens** | Visualización avanzada del historial y autoría del código |
| **GitHub Pull Requests and Issues** | Gestión de PRs sin salir de VS Code |
| **PHP Intelephense** | Autocompletado e inteligencia para PHP |
| **MySQL** (cweijan) | Consulta de base de datos desde VS Code sin acceso DDL |

---

## 7. Resumen del flujo diario

```
Pull al llegar
    → Trabajar en rama propia
        → Commits frecuentes con mensajes claros
            → Push al terminar cada bloque
                → Pull Request cuando la tarea está completa
                    → Responsable revisa y mergea a main
```

---

## Registro de cambios

| Fecha | Descripción del cambio |
|-------|------------------------|
| 2026-02-27 | Versión inicial del documento |
| 2026-02-27 | Correcciones tras prueba real: nomenclatura GitHub actualizada, proceso de ruleset detallado, nota sobre plan gratuito vs Team, paso de autenticación y apertura del repo en VS Code, proceso de PR sin botón automático |
| 2026-02-27 | Añadido proceso de revisión del código del becario sin mergear a main |
