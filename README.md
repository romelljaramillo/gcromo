# GCromo Budgets

Módulo para gestionar presupuestos (quotes) en PrestaShop 8.2.3 con convenciones modernas y componentes Symfony.

## Características

- Nueva sección en el back office dentro del menú de Clientes.
- Controlador Symfony y plantilla Twig para visualizar y gestionar presupuestos.
- Estructura preparada para ampliar con grids, formularios y lógica de negocio.
- Tablas de base de datos para presupuestos y líneas de presupuesto.

## Requisitos

- PrestaShop 8.2.3 o superior (_ps_version debe ser 8.2.3 en adelante).
- PHP 7.4 o superior.

# GCromo Budgets

Módulo para gestionar presupuestos (quotes) en PrestaShop 8.2.3 con convenciones modernas y componentes Symfony.

## Características

- Nueva sección en el back office dentro del menú de Clientes.
- Controlador Symfony y plantilla Twig para visualizar y gestionar presupuestos.
- Estructura preparada para ampliar con grids, formularios y lógica de negocio.
- Tablas de base de datos para presupuestos y líneas de presupuesto.

## Requisitos

- PrestaShop 8.2.3 o superior (`_PS_VERSION_` debe ser 8.2.3 en adelante).
- PHP 7.4 o superior.

## Instalación

1. Copia el directorio `gcromo` en la carpeta `modules/` de tu tienda.
2. Ejecuta `composer dump-autoload` en el directorio del módulo si realizas cambios en el código.
3. Instala el módulo desde el back office (`Módulos > Gestor de módulos > Subir módulo`).

Durante la instalación se crearán automáticamente las tablas necesarias y la pestaña "Presupuestos" en el menú de Clientes.

## Desinstalación

Al desinstalar el módulo se eliminarán las tablas creadas:

- `ps_gcromo_budget`
- `ps_gcromo_budget_line`

> Asegúrate de exportar los datos si los necesitas antes de desinstalar.

## Desarrollo

- Controlador Symfony principal: `Gcromo\\Controller\\Admin\\BudgetController`.
- Instalador: `Gcromo\\Install\\Installer`.
- Ruta BO: `admin_gcromo_budget_index`.

Las traducciones utilizan el dominio `Modules.Gcromo.Admin`. Puedes generar archivos XLF en `translations/` para adaptarlas a tus idiomas.

## Próximos pasos sugeridos

- Añadir grids Symfony para listar presupuestos.
- Implementar formularios para crear/editar presupuestos.
- Integración con el flujo de pedidos y clientes.
