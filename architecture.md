```
project/
│
├── public/
│   └── index.php
│
├── bootstrap/
│   └── container.php
│
├── app/
│
│   ├── Core/
│   │   ├── Router.php
│   │   ├── Kernel.php
│   │   └── Module.php
│
│   ├── Modules/
│   │
│   │   ├── Blog/
│   │   │   ├── Domain/
│   │   │   ├── Application/
│   │   │   ├── Infrastructure/
│   │   │   ├── Http/
│   │   │   │   ├── Controllers/
│   │   │   │   └── routes.php
│   │   │   └── module.php
│   │   │
│   │   ├── Pages/
│   │   ├── Users/
│   │   ├── Media/
│   │   └── Settings/
│
│   └── Shared/
│       ├── Domain/
│       ├── Infrastructure/
│       └── Http/
```