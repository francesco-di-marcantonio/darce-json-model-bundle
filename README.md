**<p align="center">Json To Model Bundle</p>**

Il bundle fornisce la possibilità di auto-generare una struttura di classi a partire da un json. Ogni classe generata conterrà:
- Attributi privati della classe con annotation @var e @JmsSerializer\Type
- Metodi get di ogni attributo generato

Se il json contiene al suo interno degli oggetti il bundle genera automaticamente classi ad hoc anche per quest'ultimi.

**Utilizzo**

Per auto-generare la struttura di classi il bundle mette a disposizione un Command `darce-json-model:make-model` che
prende in input 4 possibili argomenti:
- REQUIRED: Json di cui generare le classi
- REQUIRED: Nome della classe "root" da generare
- OPTIONAL: Path relativo in cui generare le classi (di default è 'src/Model/Api')
- OPTIONAL: Namespace in cui devono essere definite le classi (di default è 'App\\Model\\Api')

Il command prevede anche le seguenti opzioni:
- --strict-type: Aggiunge `declare(strict_types=1);` ad ogni classe generata