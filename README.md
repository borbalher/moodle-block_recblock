#Recblock, un plugin adaptativo
Recblock nace con el fin de mejorar la adaptabilidad de las interfaces de los cursos de la plataforma Moodle. Es un plug-in recomendador que se basa en los estilos de aprendizaje y juego para ofrecer una recomendación. Es capaz de detectar el estilo de juego y aprendizaje y realizar una recomendación para cada uno de estos.

#¿Qué estilos de aprendizaje y juego usa?
##Estilo de juego: MUD-1 de Richard Barlte
Se han escogido los módulos indicados por Natalie DeanMade en su guía *Moodle for Motivating Learners* para detectar el estilo de juego.  Es recomendable seguir la guía para adaptar la plataforma y las actividades.

**Moodle for Motivating Learners:**
https://www.moodlefacts.nl/wp-content/uploads/2013/06/Moodle_For_Motivation_Guide.pdf

Tipo del modulo | Achiever | Explorer | Socializer | Killer
--------------- | -------- | -------- | ---------- | ------
Tarea | 1 | 0 | 0 | 0
Chat | 0 | 0 | 1 | 0
Elección | 0 | 1 | 1 | 0
Base de datos | 0 | 0 | 0 | 0
Foro | 1 | 1 | 1 | 1
Glosario | 1 | 0 | 1 |1
Lección | 1 | 1 | 1 | 0
Cuestionario | 1 | 0 | 0 | 0
Wiki | 0 | 1 | 1 | 0
Workshop | 0 | 0 | 1 | 1

##Estilo de aprendizaje:  VARK de Neil Flemming y Collin Mills
Sólo nos interesan los tipos de actividades y recursos que ofrezcan un uso continuado y un beneficio a lo largo del tiempo. Estos son los módulos de la plataforma que usa el usuario por voluntad propia para su proceso de aprendizaje y detectan mejor sus preferencias

Tipo del modulo | Visual | Auditivo | Lector | Kinestésico
--------------- | ------ | -------- | ------ | -----------
Libro | 0 | 0 | 1 | 0
Chat | 0 | 1 | 0 | 0
Base de datos | 0 | 1 | 1 | 0
Carpeta | 1 | 1 | 1 | 0
Foro | 0 | 1 | 0 | 0
Glosario | 0 | 1 | 1 |0
IMS | 0 | 0 | 1 | 1
Página | 0 | 0 | 1 | 0
Lección | 0 | 0 | 1 | 0
LTI | 0 | 0 | 0 | 1
Cuestionario | 0 | 0 | 1 | 0
Archivo | 0 | 0 | 1 | 0
SCORM | 1 | 1 | 1 | 1
URL | 1 | 1 | 1 | 0
Wiki | 0 | 1 | 1 | 0

En el caso de los cuestionarios y lecciones solo contarán aquellos que no tengan límite de uso

##Como mejorar la ambigüedad de los tipos por defecto

A simple vista se puede detectar en la tabla que hay casos que podrían ser ambigüos y producen cierta incertidumbre:

 - Los archivos normalmente suelen ser o textos, podríamos asignarles ese valor por defecto pero, ¿Qué pasa si se sube un podcast o un  minijuego? ¿Sigue siendo relevante el tipo VARK asignado por defecto?
 -  Se ha cambiado el diseño de una actividad para adaptarla a las motivaciones de un nuevo tipo de jugador ¿No debería quedar reflejado?

Con el fin de evitar esto se ha establecido un sistema de etiquetas para especificar el vector binario de características mediante el uso de estas en el nombre del modulo.  Las etiquetas llevan la siguiente nomenclatura:

 **- {EA:[VARK]+}** Etiqueta para estilo de aprendizaje. Ejemplos: {EA: VA}, {EA:VAR}, {EA:K}.....
	 - [VARK]+	: 1 o más letras del conjunto {V,A,R,K}.
 **- {EJ:[AESK]+}** Etiqueta para estilo de juego. Ejemplos: {EJ:EA}, {EJ: AK},{EJ: ESK}....
	 - [AESK]+	: 1 o más letras del conjunto  {A,E,S,K}.

## Posibles futuras mejoras

 - Creación de un sistema de registro para saber si el usuario ha seguido nuestra recomendación.
 - Almanecar el perfil de estilo de juego y de aprendizaje del alumno en la base de datos.
 - Informes para el profesorado
 - Crear nuevos tipos de perfiles de usuario y de items.
 - Adaptarlo para soportar filtrado colaborativo basado en usuarios.
 - Introducir mas estilos de juegos y aprendizaje.
 - Mejoras visuales de presentación del bloque.
 - Separar el plug-in del recomendador y que se comunique con este por medio de una API
