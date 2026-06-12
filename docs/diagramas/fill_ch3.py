# -*- coding: utf-8 -*-
import io

path = r"C:/Users/Dell/AppData/Roaming/Claude/local-agent-mode-sessions/skills-plugin/cdcf0309-2ac2-4168-992a-a5fc1fbe8258/01da8d60-96fe-47e5-9ef6-f7c13e026192/skills/docx/unpacked/word/document.xml"
with io.open(path, "r", encoding="utf-8") as f:
    content = f.read()

left_anchor = '<w:bookmarkEnd w:id="48"/>\n    </w:p>\n'
right_anchor = '    <w:p w14:paraId="2B1ECF6B"'

i = content.index(left_anchor) + len(left_anchor)
j = content.index(right_anchor)

def img_para(cx, cy, rid, docid, name):
    return (
'    <w:p>\n'
'      <w:pPr><w:jc w:val="center"/></w:pPr>\n'
'      <w:r>\n'
'        <w:rPr><w:noProof/></w:rPr>\n'
'        <w:drawing>\n'
'          <wp:inline distT="0" distB="0" distL="0" distR="0">\n'
'            <wp:extent cx="' + str(cx) + '" cy="' + str(cy) + '"/>\n'
'            <wp:effectExtent l="0" t="0" r="0" b="0"/>\n'
'            <wp:docPr id="' + str(docid) + '" name="' + name + '"/>\n'
'            <wp:cNvGraphicFramePr>\n'
'              <a:graphicFrameLocks xmlns:a="http://schemas.openxmlformats.org/drawingml/2006/main" noChangeAspect="1"/>\n'
'            </wp:cNvGraphicFramePr>\n'
'            <a:graphic xmlns:a="http://schemas.openxmlformats.org/drawingml/2006/main">\n'
'              <a:graphicData uri="http://schemas.openxmlformats.org/drawingml/2006/picture">\n'
'                <pic:pic xmlns:pic="http://schemas.openxmlformats.org/drawingml/2006/picture">\n'
'                  <pic:nvPicPr>\n'
'                    <pic:cNvPr id="' + str(docid) + '" name="' + name + '"/>\n'
'                    <pic:cNvPicPr/>\n'
'                  </pic:nvPicPr>\n'
'                  <pic:blipFill>\n'
'                    <a:blip r:embed="' + rid + '"/>\n'
'                    <a:stretch><a:fillRect/></a:stretch>\n'
'                  </pic:blipFill>\n'
'                  <pic:spPr>\n'
'                    <a:xfrm><a:off x="0" y="0"/><a:ext cx="' + str(cx) + '" cy="' + str(cy) + '"/></a:xfrm>\n'
'                    <a:prstGeom prst="rect"><a:avLst/></a:prstGeom>\n'
'                  </pic:spPr>\n'
'                </pic:pic>\n'
'              </a:graphicData>\n'
'            </a:graphic>\n'
'          </wp:inline>\n'
'        </w:drawing>\n'
'      </w:r>\n'
'    </w:p>\n'
    )

def body(text):
    return '    <w:p>\n      <w:r>\n        <w:t xml:space="preserve">' + text + '</w:t>\n      </w:r>\n    </w:p>\n'

def caption(text):
    return '    <w:p>\n      <w:pPr><w:pStyle w:val="Descripcin"/></w:pPr>\n      <w:r>\n        <w:t xml:space="preserve">' + text + '</w:t>\n      </w:r>\n    </w:p>\n'

def section(num, title):
    return ('    <w:p>\n      <w:pPr><w:ind w:left="284" w:hanging="284"/></w:pPr>\n'
            '      <w:r><w:rPr><w:b/><w:bCs/></w:rPr><w:t xml:space="preserve">' + num + '</w:t></w:r>\n'
            '      <w:r><w:rPr><w:b/><w:bCs/></w:rPr><w:tab/><w:t xml:space="preserve">' + title + '</w:t></w:r>\n    </w:p>\n')

def subsection(num, title):
    return ('    <w:p>\n      <w:pPr><w:ind w:left="426" w:hanging="426"/></w:pPr>\n'
            '      <w:r><w:rPr><w:b/><w:bCs/></w:rPr><w:t xml:space="preserve">' + num + '</w:t></w:r>\n'
            '      <w:r><w:rPr><w:b/><w:bCs/></w:rPr><w:tab/><w:t xml:space="preserve">' + title + '</w:t></w:r>\n    </w:p>\n')

def bullet(numid, lead, rest):
    return ('    <w:p>\n      <w:pPr><w:pStyle w:val="Prrafodelista"/><w:numPr><w:ilvl w:val="0"/><w:numId w:val="' + str(numid) + '"/></w:numPr></w:pPr>\n'
            '      <w:r><w:rPr><w:b/><w:bCs/></w:rPr><w:t xml:space="preserve">' + lead + '</w:t></w:r>\n'
            '      <w:r><w:t xml:space="preserve">' + rest + '</w:t></w:r>\n    </w:p>\n')

LQ = "“"
RQ = "”"
LA = "«"
RA = "»"

new = ""
new += body("El presente capítulo detalla el modelo de implementación de la plataforma CompuWeb desde la perspectiva del análisis y diseño orientado a objetos. Mediante el Lenguaje Unificado de Modelado (UML) se representan, por un lado, la interacción entre los actores del sistema y las funcionalidades disponibles a través del diagrama de casos de uso y, por otro, la estructura estática del dominio y sus relaciones mediante el diagrama de clases. Ambos modelos reflejan la arquitectura multitenencia y la lógica de negocio efectivamente implementada en el sistema.")

new += section("1.", "Diagrama de casos de uso")
new += body("El diagrama de casos de uso describe el comportamiento del sistema desde la perspectiva de sus usuarios, identificando los actores que interactúan con la plataforma y las funcionalidades (casos de uso) que cada uno puede ejecutar. En CompuWeb se distinguen tres actores con distintos niveles de privilegio, alineados con el esquema de autenticación OAuth2 gestionado por Laravel Passport.")
new += img_para(4222100, 7315200, "rId14", 100, "Diagrama de Casos de Uso")
new += caption("Figura 2. Diagrama de Casos de Uso. Fuente: Elaboración propia.")

new += subsection("1.1", "Actores del sistema")
new += bullet(23, "Cliente Final: ", "usuario público que accede al catálogo de una tienda mediante su slug, sin necesidad de autenticación. Puede explorar categorías y productos, consultar fichas técnicas y disponibilidad en tiempo real, armar un carrito de reserva y cerrar la compra de forma asistida seleccionando a un vendedor para ser redirigido a WhatsApp.")
new += bullet(23, "Vendedor: ", "usuario autenticado mediante token de acceso. Gestiona el ciclo de ventas de su tienda: registra ventas, consulta el historial y el detalle de cada transacción, y descarga el recibo en formato PDF.")
new += bullet(23, "Administrador: ", "usuario con rol privilegiado que hereda todas las capacidades del vendedor y, adicionalmente, administra el catálogo (productos y categorías), registra entradas de mercadería, gestiona usuarios y roles, genera reportes analíticos y configura los parámetros de su tienda.")

new += subsection("1.2", "Casos de uso principales")
new += body("El caso de uso central del sistema es " + LQ + "Registrar venta" + RQ + ", el cual incorpora de manera obligatoria (relación " + LA + "include" + RA + ") el descuento de stock mediante una transacción atómica y el registro del movimiento de inventario asociado. De esta forma se garantiza que, ante cada venta confirmada, la existencia física y la vitrina digital permanezcan sincronizadas, actualizando automáticamente el estado del producto a " + LQ + "Agotado" + RQ + " cuando su stock llega a cero. Por su parte, el cierre de la venta asistida del Cliente Final incluye la selección del especialista, paso que permite formatear dinámicamente el enlace de redirección a WhatsApp con el pedido estructurado.")

new += section("2.", "Diagrama de clases")
new += body("El diagrama de clases representa la estructura estática del modelo de dominio; es decir, las entidades persistentes del sistema (implementadas como modelos Eloquent), sus atributos, sus métodos de relación y las asociaciones entre ellas. Su organización refleja directamente el esquema relacional de la base de datos y la estrategia de aislamiento multitenencia adoptada.")
new += img_para(5970000, 5948867, "rId15", 101, "Diagrama de Clases")
new += caption("Figura 3. Diagrama de Clases. Fuente: Elaboración propia.")

new += subsection("2.1", "Entidades del dominio")
new += body("El modelo se articula en torno a la entidad Tienda, raíz de la multitenencia, de la cual dependen todas las demás entidades a través del identificador tienda_id. Las clases principales son:")
new += bullet(23, "Tienda: ", "representa a cada comercio afiliado o inquilino. Almacena los datos comerciales como nombre, slug único, NIT y logo.")
new += bullet(23, "User: ", "usuarios del sistema (administradores y vendedores), diferenciados por el atributo role. Incluye los campos whatsapp_number y visible_catalogo que habilitan el comercio conversacional.")
new += bullet(23, "Producto y Categoria: ", "conforman el catálogo segmentado. Producto mantiene los niveles de stock, precio, precio de costo y estado de disponibilidad.")
new += bullet(23, "AtributoProducto y UnidadMedida: ", "aportan la flexibilidad de esquema, permitiendo atributos dinámicos y unidades de medida variables para adaptar el catálogo a distintos rubros comerciales.")
new += bullet(23, "Venta y DetalleVenta: ", "registran las transacciones y sus líneas de detalle (producto, cantidad y precio unitario), así como el método de pago y el estado de la venta.")
new += bullet(23, "Cliente: ", "almacena los datos de los compradores registrados de cada tienda.")
new += bullet(23, "MovimientoInventario: ", "bitácora de auditoría que registra cada entrada y salida de stock, vinculando el producto, el usuario responsable y la referencia de la operación.")

new += subsection("2.2", "Relaciones y aislamiento multitenencia")
new += body("Las asociaciones son mayoritariamente de tipo uno a muchos. Una Tienda agrupa múltiples usuarios, categorías, productos, clientes y ventas; una Venta se compone de varios DetalleVenta; y cada Producto puede figurar en múltiples detalles de venta y movimientos de inventario. El aislamiento de datos entre inquilinos no se delega a la capa de controladores, sino que se implementa de forma transversal en la capa del modelo mediante el TiendaScope, un Global Scope de Eloquent que filtra automáticamente toda consulta por el tienda_id del usuario autenticado, garantizando que ningún comercio pueda acceder a la información de otro.")

content = content[:i] + new + content[j:]

with io.open(path, "w", encoding="utf-8") as f:
    f.write(content)

print("OK - reemplazo realizado. Longitud nuevo bloque:", len(new))
