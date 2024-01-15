<?php
// This file is part of the bloockcert module for Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Language strings for the bloockcert module.
 *
 * @package    mod_bloockcert
 * @copyright  2024 Edwin Martinez <desarrollo@emprove.com.mx>
 * @copyright  2013 based on work by Mark Nelson <markn@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$string['activity'] = 'Actividad';
$string['addcertpage'] = 'Añadir página';
$string['addelement'] = 'Añadir elemento';
$string['aligncenter'] = 'Centrado';
$string['alignleft'] = 'Alineación a la izquierda';
$string['alignment'] = 'Alineación';
$string['alignment_help'] = 'Esta propiedad establece la alineación horizontal del elemento. Es posible que algunos elementos no respalden esto, mientras que el comportamiento de otros puede diferir.';
$string['alignright'] = 'Alineación a la derecha';
$string['awardedto'] = 'Adjudicado a';
$string['cannotverifyallcertificates'] = 'No tiene permiso para verificar todos los certificados en el sitio.';
$string['certificate'] = 'Certificado';
$string['code'] = 'Código';
$string['copy'] = 'Copiar';
$string['coursetimereq'] = 'Minutos requeridos en el curso';
$string['coursetimereq_help'] = 'Ingrese aquí la cantidad mínima de tiempo, en minutos, que un estudiante debe iniciar sesión en el curso antes de poder recibir
el certificado.';
$string['createtemplate'] = 'Crear plantilla';
$string['bloockcert:addinstance'] = 'Agregar una nueva instancia de certificado Bloock';
$string['bloockcert:manage'] = 'Administrar un certificado Bloock';
$string['bloockcert:manageemailstudents'] = 'Administrar la configuración de correo electrónico de los estudiantes';
$string['bloockcert:manageemailteachers'] = 'Administrar la configuración de correo electrónico de los profesores';
$string['bloockcert:manageemailothers'] = 'Administrar la configuración de correo electrónico de otros';
$string['bloockcert:manageverifyany'] = 'Administrar la configuración de verificación';
$string['bloockcert:managerequiredtime'] = 'Administrar la configuración del tiempo requerido';
$string['bloockcert:manageprotection'] = 'Administrar la configuración de protección';
$string['bloockcert:receiveissue'] = 'Recibe un certificado';
$string['bloockcert:view'] = 'Ver un certificado de Bloock';
$string['bloockcert:viewreport'] = 'Ver informe del curso';
$string['bloockcert:viewallcertificates'] = 'Ver todos los certificados';
$string['bloockcert:verifyallcertificates'] = 'Verificar todos los certificados en el sitio';
$string['bloockcert:verifycertificate'] = 'Verificar un certificado';
$string['bloockcertsettings'] = 'Configuraciones de Certificados Bloock';
$string['deletecertpage'] = 'Eliminar página';
$string['deleteconfirm'] = 'Eliminar confirmación';
$string['deleteelement'] = 'Eliminar elemento';
$string['deleteelementconfirm'] = '¿Estás seguro de que deseas eliminar este elemento?';
$string['deleteissueconfirm'] = '¿Está seguro de que desea eliminar esta emisión de certificado?';
$string['deleteissuedcertificates'] = 'Eliminar certificados emitidos';
$string['deletepageconfirm'] = '¿Está seguro de que desea eliminar esta página de certificado?';
$string['deletetemplateconfirm'] = '¿Está seguro de que desea eliminar esta plantilla de certificado?';
$string['deliveryoptiondownload'] = 'Enviar al navegador y forzar la descarga de un archivo';
$string['deliveryoptioninline'] = 'Enviar el archivo en línea al navegador';
$string['deliveryoptions'] = 'Opciones de entrega';
$string['description'] = 'Descripción';
$string['duplicate'] = 'Duplicar';
$string['duplicateconfirm'] = 'Confirmación duplicada';
$string['duplicatetemplateconfirm'] = '¿Está seguro de que desea duplicar esta plantilla de certificado?';
$string['editbloockcert'] = 'Editar certificado';
$string['editelement'] = 'Editar elemento';
$string['edittemplate'] = 'Editar plantilla';
$string['elementheight'] = 'Altura';
$string['elementheight_help'] = 'Especifique la altura del elemento. Si se permite \'0\' se calcula automáticamente.';
$string['elementname'] = 'Nombre del elemento';
$string['elementname_help'] = 'Este será el nombre utilizado para identificar este elemento al editar un certificado. Nota: esto no se mostrará en el PDF.';
$string['elementplugins'] = 'Complementos de elementos';
$string['elements'] = 'Elementos';
$string['elements_help'] = 'Esta es la lista de elementos que se mostrarán en el certificado.

Tenga en cuenta: los elementos se representan en este orden. El orden se puede cambiar usando las flechas al lado de cada elemento.';
$string['elementwidth'] = 'Ancho';
$string['elementwidth_help'] = 'Especifique el ancho del elemento. Si se permite \'0\' se calcula automáticamente.';
$string['emailnonstudentbody'] = 'Adjunto se encuentra el certificado \'{$a->certificatename}\' para \'{$a->userfullname}\' para el curso \'{$a->coursefullname}\'.';
$string['emailnonstudentbodyplaintext'] = 'Adjunto se encuentra el certificado \'{$a->certificatename}\' para \'{$a->userfullname}\' para el curso \'{$a->coursefullname}\'.';
$string['emailnonstudentcertificatelinktext'] = 'Ver informe de certificado';
$string['emailnonstudentgreeting'] = 'Hola';
$string['emailnonstudentsubject'] = '{$a->coursefullname}: {$a->certificatename}';
$string['emailstudentbody'] = 'Adjunto está su certificado \'{$a->certificatename}\' para el curso \'{$a->coursefullname}\'.';
$string['emailstudentbodyplaintext'] = 'Adjunto está su certificado \'{$a->certificatename}\' para el curso \'{$a->coursefullname}\'.';
$string['emailstudentcertificatelinktext'] = 'Ver Certificado';
$string['emailstudentgreeting'] = 'Estimado {$a}';
$string['emailstudentsubject'] = '{$a->coursefullname}: {$a->certificatename}';
$string['emailstudents'] = 'Email estudiantes';
$string['emailstudents_help'] = 'Si se configura, se enviará por correo electrónico a los estudiantes una copia del certificado cuando esté disponible. <strong>Advertencia:</strong> Establecer esto en \'Sí\' antes de haber terminado de crear el certificado enviará al estudiante por correo electrónico un certificado incompleto.';
$string['emailteachers'] = 'Email maestros';
$string['emailteachers_help'] = 'Si se configura, se enviará por correo electrónico a los profesores una copia del certificado cuando esté disponible. <strong>Advertencia:</strong> Si establece esto en \'Sí\' antes de haber terminado de crear el certificado, se enviará por correo electrónico al profesor un certificado incompleto.';
$string['emailothers'] = 'Email otros';
$string['urlclient'] = 'URL del Cliente';
$string['emailothers_help'] = 'Si se configura, se enviará por correo electrónico a las direcciones de correo electrónico enumeradas aquí (separadas por una coma) una copia del certificado cuando esté disponible. <strong>Advertencia:</strong> Al establecer este campo antes de haber terminado de crear el certificado, se enviará por correo electrónico a las direcciones un certificado incompleto.';
$string['urlclient_help'] = 'Esta es la URL a donde se envían los certificados para comenzar el proceso de firma con blockchain.';
$string['eventelementcreated'] = 'Elemento de certificado bloock creado';
$string['eventelementdeleted'] = 'Elemento de certificado bloock eliminado';
$string['eventelementupdated'] = 'Elemento de certificado bloock actualizado';
$string['eventpagecreated'] = 'Página de certificado bloock creada';
$string['eventpagedeleted'] = 'Página de certificado bloock eliminada';
$string['eventpageupdated'] = 'Página de certificado bloock actualizada';
$string['eventtemplatecreated'] = 'Plantilla de certificado bloock creada';
$string['eventtemplatedeleted'] = 'Plantilla de certificado bloock eliminada';
$string['eventtemplateupdated'] = 'Plantilla de certificado bloock actualizada';
$string['exampledatawarning'] = 'Algunos de estos valores pueden ser solo un ejemplo para garantizar que sea posible el posicionamiento de los elementos.';
$string['font'] = 'Fuente';
$string['font_help'] = 'La fuente utilizada al generar este elemento.';
$string['fontcolour'] = 'Color';
$string['fontcolour_help'] = 'El color de la fuente.';
$string['fontsize'] = 'Tamaño';
$string['fontsize_help'] = 'El tamaño de la fuente en puntos.';
$string['getbloockcert'] = 'Obtener certificado';
$string['gradeoutcome'] = 'Resultado';
$string['height'] = 'Altura';
$string['height_help'] = 'Esta es la altura del certificado PDF en mm. Como referencia, una hoja de papel A4 tiene 297 mm de alto y una carta tiene 279 mm de alto.';
$string['invalidcode'] = 'Se ha proporcionado un código no válido.';
$string['invalidcolour'] = 'El color elegido no es válido. Introduzca un nombre de color HTML válido o un color hexadecimal de seis o tres dígitos.';
$string['invalidelementwidthorheightnotnumber'] = 'Por favor ingrese un número valido.';
$string['invalidelementwidthorheightzeroallowed'] = 'Por favor ingrese un número mayor o igual a 0.';
$string['invalidelementwidthorheightzeronotallowed'] = 'Por favor ingrese un número mayor que 0.';
$string['invalidposition'] = 'Por favor seleccione un número positivo para la posición {$a}.';
$string['invalidheight'] = 'La altura tiene que ser un número válido mayor que 0.';
$string['invalidmargin'] = 'El margen tiene que ser un número válido mayor que 0.';
$string['invalidwidth'] = 'El ancho tiene que ser un número válido mayor que 0.';
$string['landscape'] = 'Paisaje';
$string['leftmargin'] = 'Margen izquierdo';
$string['leftmargin_help'] = 'Este es el margen izquierdo del PDF del certificado en mm.';
$string['listofissues'] = 'Destinatarios: {$a}';
$string['load'] = 'Carga';
$string['loadtemplate'] = 'Cargar plantilla';
$string['loadtemplatemsg'] = '¿Está seguro de que desea cargar esta plantilla? Esto eliminará cualquier página y elemento existente para este certificado.';
$string['managetemplates'] = 'Administrar plantillas';
$string['managetemplatesdesc'] = 'Este enlace lo llevará a una nueva pantalla donde podrá administrar las plantillas utilizadas por las actividades del certificado Blockock en los cursos.';
$string['modify'] = 'Modificar';
$string['modulename'] = 'Certificado bloock';
$string['modulenameplural'] = 'Certificados bloock';
$string['modulename_help'] = 'Este módulo permite la generación dinámica de certificados PDF firmados a por medio de blockchain.';
$string['modulename_link'] = 'Bloock_certificate_module';
$string['mycertificates'] = 'Mis certificados bloockcert';
$string['mycertificatesdescription'] = 'Estos son los certificados que le han emitido por correo electrónico o descargándolos manualmente.';
$string['name'] = 'Nombre';
$string['nametoolong'] = 'Has excedido la longitud máxima permitida para el nombre.';
$string['nobloockcerts'] = 'No hay certificados para este curso.';
$string['noimage'] = 'Sin imágen';
$string['norecipients'] = 'No hay destinatarios';
$string['notemplates'] = 'Sin plantillas';
$string['notissued'] = 'No premiado';
$string['notverified'] = 'No verificado';
$string['options'] = 'Opciones';
$string['page'] = 'Página {$a}';
$string['pluginadministration'] = 'Administración de certificados bloock';
$string['pluginname'] = 'Certificados Bloock';
$string['portrait'] = 'Retrato';
$string['posx'] = 'Posición X';
$string['posx_help'] = 'Esta es la posición en mm desde la esquina superior izquierda donde desea que se ubique el punto de referencia del elemento en la dirección x.';
$string['posy'] = 'Posición Y';
$string['posy_help'] = 'Esta es la posición en mm desde la esquina superior izquierda que desea que ubique el punto de referencia del elemento en la dirección y.';
$string['preventcopy'] = 'Prevenir copia';
$string['preventcopy_desc'] = 'Habilite la protección contra la acción de copia.';
$string['preventprint'] = 'Impedir impresión';
$string['preventprint_desc'] = 'Habilite la protección contra la acción de impresión.';
$string['preventmodify'] = 'Prevenir modificar';
$string['preventmodify_desc'] = 'Habilite la protección contra la acción de modificación.';
$string['print'] = 'Imprimir';
$string['privacy:metadata:bloockcert_issues'] = 'La lista de certificados emitidos.';
$string['privacy:metadata:bloockcert_issues:code'] = 'El código que pertenece al certificado.';
$string['privacy:metadata:bloockcert_issues:bloockcertid'] = 'El ID del certificado';
$string['privacy:metadata:bloockcert_issues:emailed'] = 'Si el certificado fue enviado por correo electrónico o no';
$string['privacy:metadata:bloockcert_issues:timecreated'] = 'La hora en que se emitió el certificado.';
$string['privacy:metadata:bloockcert_issues:userid'] = 'El ID del usuario al que se le emitió el certificado.';
$string['rearrangeelements'] = 'Reposicionar elementos';
$string['rearrangeelementsheading'] = 'Arrastre y suelte elementos para cambiar su ubicación en el certificado.';
$string['receiveddate'] = 'Otorgado el';
$string['refpoint'] = 'Ubicación del punto de referencia';
$string['refpoint_help'] = 'El punto de referencia es la ubicación de un elemento a partir del cual se determinan sus coordenadas xey. Se indica con el \'+\' que aparece en el centro o en las esquinas del elemento.';
$string['replacetemplate'] = 'Reemplazar';
$string['requiredtimenotmet'] = 'Debe dedicar al menos un mínimo de {$a->requiredtime} minutos en el curso antes de poder acceder a este certificado.';
$string['rightmargin'] = 'Margen derecho';
$string['rightmargin_help'] = 'Este es el margen derecho del PDF del certificado en mm.';
$string['save'] = 'Guardar';
$string['saveandclose'] = 'Guardar y cerrar';
$string['saveandcontinue'] = 'Guardar y continuar';
$string['savechangespreview'] = 'Guardar cambios y obtener vista previa';
$string['savetemplate'] = 'Guardar plantilla';
$string['search:activity'] = 'Certificado bloock - información de actividad';
$string['setprotection'] = 'Establecer protección';
$string['setprotection_help'] = 'Elija las acciones que desea evitar que los usuarios realicen en este certificado.';
$string['showposxy'] = 'Mostrar posición X e Y';
$string['showposxy_desc'] = 'Esto mostrará la posición X e Y al editar un elemento, lo que permitirá al usuario especificar la ubicación con precisión.

Esto no es necesario si planeas usar únicamente la interfaz de arrastrar y soltar para este propósito.';
$string['taskemailcertificate'] = 'Maneja el envío de certificados por correo electrónico.';
$string['templatename'] = 'Nombre de la plantilla';
$string['templatenameexists'] = 'Ese nombre de plantilla está actualmente en uso, elija otro.';
$string['topcenter'] = 'Centro';
$string['topleft'] = 'Arriba a la izquierda';
$string['topright'] = 'Parte superior derecha';
$string['type'] = 'Tipo';
$string['uploadimage'] = 'Cargar imagen';
$string['uploadimagedesc'] = 'Este enlace lo llevará a una nueva pantalla donde podrá cargar imágenes. Imágenes cargadas usando
Este método estará disponible en todo su sitio para todos los usuarios que puedan crear un certificado.';
$string['verified'] = 'Verificado';
$string['verify'] = 'Verificar';
$string['verifyallcertificates'] = 'Permitir la verificación de todos los certificados.';
$string['verifyallcertificates_desc'] = 'Cuando esta configuración está habilitada, cualquier persona (incluidos los usuarios que no hayan iniciado sesión) puede visitar el enlace \'{$a}\' para verificar cualquier certificado en el sitio, en lugar de tener que ir al enlace de verificación de cada certificado.

Nota: esto solo se aplica a los certificados en los que \'Permitir que cualquiera verifique un certificado\' se ha configurado en \'Sí\' en la configuración del certificado.';
$string['verifycertificate'] = 'Verificar certificado';
$string['verifycertificatedesc'] = 'Este enlace lo llevará a una nueva pantalla donde podrá verificar los certificados en el sitio.';
$string['verifycertificateanyone'] = 'Permitir que cualquiera verifique un certificado';
$string['verifycertificateanyone_help'] = 'Esta configuración permite que cualquier persona con el enlace de verificación del certificado (incluidos los usuarios que no hayan iniciado sesión) verifique un certificado.';
$string['width'] = 'Ancho';
$string['width_help'] = 'Este es el ancho del PDF del certificado en mm. Como referencia, una hoja de papel A4 tiene 210 mm de ancho y una carta tiene 216 mm de ancho.';

$string['verifyurl'] = 'Verificar URL';
$string['verifyurldesc'] = 'Este enlace lo llevará a una nueva pantalla donde podrá verificar su URL.';

$string['userlanguage'] = 'Usar preferencias de usuario';
$string['languageoptions'] = 'Forzar lenguaje de certificado';
$string['userlanguage_help'] = 'Puede forzar que el idioma del certificado anule las preferencias de idioma del usuario.';

// Acess API.
$string['bloockcert:managelanguages'] = 'Administrar idioma en el formulario de edición';
