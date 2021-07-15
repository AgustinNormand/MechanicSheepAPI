# MechanicSheep Migrator

## Explicación
Aplicación java que lee datos historicos (1998-2021) de una empresa (MechanicSheep), de 4 archivos DBASE (.dbf) *Clientes*, *Vehiculos*, *Trabajos*, *Detalles*,
y los migra a una base de datos relacional, normalizada.

La cual tiene las tablas Vehiculos, Marcas, Modelos, Tipos Documentos, Personas, Trabajos, Empleados, etc.

Se pasó de tener unicamente 4 archivos, a dividir los datos para llegar varias tablas normalizadas.

Dichos archivos no tenian claves primarias, existian *Detalles* con numeros de trabajo que no existían en el archivo de *Trabajos*, y varias complicaciónes más, similares a la mencionada.

Se realizó un código capaz de migrar la mayor cantidad de datos posibles, para realizar un sistema nuevo, conservando gran parte de los datos historicos.
