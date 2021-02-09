# plinct cms
### CMS for Plinct application

Admin panel to manage API Plinct

According to Schema.org standards

Enabled types:
1. User;
2. Person;
3. PostalAddress;
4. ContactPoint;
5. Place;
6. Organization;
7. LocalBusiness;
8. ImageObject;
9. WebPage (with WebPageElement);
10. Product;
11. Taxon;
12. Event;
13. Order;
14. Invoice

>###Installation
>`composer install plinct/cms`

>###Dependencies
>- php: >=7.2
>- Slim/slim: 4.*
>- plinct/api
>- plinct/web
>- plinct/middleware
>- plinct/tool

>###Get starting
>On index.php
>````
><?php
>declare(strict_types=1);
>
>use \Slim\Factory\AppFactory;
>use Plinct\Middleware\RemoveEndBar;
>use Plinct\Middleware\RedirectHttps;
>use Plinct\Api\PlinctApiFactory;
>use Plinct\Cms\CmsFactory;
>
>// autoload
>include '../vendor/autoload.php';
> 
>// error
>error_reporting(E_ALL);
> 
>/******* SLIM ********/
>$slimApp = AppFactory::create();
>// for enable routes PUT and DELETE
>$slimApp->addBodyParsingMiddleware();
>  
>//******* PLINCT *********/
>// middlewares
>$slimApp->add(new RedirectHttps());
>$slimApp->add(new RemoveEndBar());
>// api
>$api = PlinctApiFactory::create($slimApp);
>$api->setAdminData("dbAdminUser", "dbEmailUser", "dbAdminPassword"); // optional
>$api->connect("dbDriver", "dbHost", "dbDatabase", "dbUser", "dbPassword");
>$api->run();
>
>// cms on https://domain/admin
>$cms = CmsFactory::create($slimApp); 
>$cms->setLanguage("pt_BR");
>$cms->setTitle("Pirenópolis Hospedagem");
>$cms->setTypesEnabled([ "webPage", "product" ]);
>$cms->run();
>
>//******* END PLINCT ***********/
>
>// public routes for website on https://domain
>$publicRoutes = include './App/routes.php';
>$publicRoutes($slimApp);
> 
>// run
>$slimApp->run();
>``
