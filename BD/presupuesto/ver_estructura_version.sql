-- Ver estructura de presupuesto_version
DESCRIBE presupuesto_version;

-- Ver campos disponibles
SHOW COLUMNS FROM presupuesto_version;

-- Ver un registro de ejemplo
SELECT * FROM presupuesto_version WHERE id_version_presupuesto = 2;
