# prueba-tecnica
======================  
Versiones:  
======================  
Symfony 6.4.29   
PHP  8.2.12  


======================  
Pasos de instalación:  
======================  
1.- clonar el proyecto  
2.- Crear una base de datos en mysql llamada quote   
3.- Migrar la bd con el siguiente comando   
    php bin/console make:migration   

5.- Levantar el servicio con el siguiente comando    
    symfony server:start   

6.- Enviar la petición desde postman   
POST 127.0.0.1:8000/api/v1/quotes  

{
  "originZipcode": "12345",
  "destinationZipcode": "54321"
}


======================   
Patrones de diseño   
======================   
Strategy , se crea interface para el manejo de las paqueterias, asi al agregar una nueva solo hay que configurarla en el service.yml 