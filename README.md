# prueba-tecnica
Pasos de instalación
1.- clonar el proyecto
2.- Crear una base de datos en mysql llamada paqueterias
3.- Levantar el servicio con el siguiente comando symfony server:start
4.- Enviar la petición desde postman
POST 127.0.0.1:8000/quotes

{
  "originZipcode": "12345",
  "destinationZipcode": "54321"
}
