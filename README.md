Laminas REST
=======

[![Build Status](https://github.com/laminas-api-tools/api-tools-rest/actions/workflows/continuous-integration.yml/badge.svg)](https://github.com/laminas-api-tools/api-tools-rest/actions/workflows/continuous-integration.yml)

> ## 🇷🇺 Русским гражданам
>
> Мы, участники Laminas, родились и живем в разных странах. У многих из нас есть друзья, родственники и коллеги как в России, так и в Украине. Некоторые из нас родились в России. Некоторые из нас живут в России. У некоторых бабушки и дедушки сражались с фашистами во Второй мировой войне. Здесь никто не поддерживает фашизм.
>
> У одного из нас есть украинская родственница, которая спаслась из дома вместе с сыном. Поезд задержался из-за бомбежки на дороге впереди. У нас есть друзья, которые прячутся в бомбоубежищах. Мы с тревогой ждем весточки от них после воздушных налетов, которые беспорядочно наносят удары и попадают по больницам, школам, детским садам и домам. Мы не берем это из каких-либо СМИ. Мы наблюдаем это напрямую.
>
> Вы доверяете нам достаточно, чтоб использовать наши программы, и мы просим вас довериться нам вновь. Мы нуждаемся в помощи. Выходите и протестуйте против этой бесполезной войны. Остановите кровопролитие. Скажите "Нет войне!"
>
> ## 🇺🇸 To Citizens of Russia
>
> We at Laminas come from all over the world. Many of us have friends, family and colleagues in both Russia and Ukraine. Some of us were born in Russia. Some of us currently live in Russia. Some have grandparents who fought Nazis in World War II. Nobody here supports fascism.
>
> One team member has a Ukrainian relative who fled her home with her son. The train was delayed due to bombing on the road ahead. We have friends who are hiding in bomb shelters. We anxiously follow up on them after the air raids, which indiscriminately fire at hospitals, schools, kindergartens and houses. We're not taking this from any media. These are our actual experiences.
>
> You trust us enough to use our software. We ask that you trust us to say the truth on this. We need your help. Go out and protest this unnecessary war. Stop the bloodshed. Say "stop the war!"

Introduction
------------

This module provides structure and code for quickly implementing RESTful APIs
that use JSON as a transport.

It allows you to create RESTful JSON APIs that use the following standards:

- [Hypermedia Application Language](http://tools.ietf.org/html/draft-kelly-json-hal-06), aka HAL,
  used for creating JSON payloads with hypermedia controls.
- [Problem Details for HTTP APIs](http://tools.ietf.org/html/draft-nottingham-http-problem-06),
  aka API Problem, used for reporting API problems.

Requirements
------------
  
Please see the [composer.json](composer.json) file.

Installation
------------

Run the following `composer` command:

```console
$ composer require laminas-api-tools/api-tools-rest
```

Alternately, manually add the following to your `composer.json`, in the `require` section:

```javascript
"require": {
    "laminas-api-tools/api-tools-rest": "^1.3"
}
```

And then run `composer update` to ensure the module is installed.

Finally, add the module name to your project's `config/application.config.php` under the `modules`
key:

```php
return [
    /* ... */
    'modules' => [
        /* ... */
        'Laminas\ApiTools\Rest',
    ],
    /* ... */
];
```

> ### laminas-component-installer
>
> If you use [laminas-component-installer](https://github.com/laminas/laminas-component-installer),
> that plugin will install api-tools-rest as a module for you.

Configuration
=============

### User Configuration

The top-level key used to configure this module is `api-tools-rest`.

#### Key: Controller Service Name

Each key under `api-tools-rest` is a controller service name, and the value is an array with one or more of
the following keys.

##### Sub-key: `collection_http_methods`

An array of HTTP methods that are allowed when making requests to a collection.

##### Sub-key: `entity_http_methods`

An array of HTTP methods that are allowed when making requests for entities.

##### Sub-key: `collection_name`

The name of the embedded property in the representation denoting the collection.

##### Sub-key: `collection_query_whitelist` (optional)

An array of query string arguments to whitelist for collection requests and when generating links
to collections. These parameters will be passed to the resource class' `fetchAll()` method. Any of
these parameters present in the request will also be used when generating links to the collection.

Examples of query string arguments you may want to whitelist include "sort", "filter", etc.

**Starting in 1.5.0**: if a input filter exists for the `GET` HTTP method, its
keys will be merged with those from configuration.

##### Sub-key: `controller_class` (optional)

An alternate controller class to use when creating the controller service; it **must** extend
`Laminas\ApiTools\Rest\RestController`. Only use this if you are altering the workflow present in the
`RestController`.

##### Sub-key: `identifier` (optional)

The name of event identifier for controller. It allows multiple instances of controller to react
to different sets of shared events.

##### Sub-key: `resource_identifiers` (optional)

The name or an array of names of event identifier/s for resource.

##### Sub-key: `entity_class`

The class to be used for representing an entity.  Primarily useful for introspection (for example in
the Laminas API Tools Admin UI).

##### Sub-key: `route_name`

The route name associated with this REST service.  This is utilized when links need to be generated
in the response.

##### Sub-key: `route_identifier_name`

The parameter name for the identifier in the route specification.

##### Sub-key: `listener`

The resource class that will be dispatched to handle any collection or entity requests.

##### Sub-key: `page_size`

The number of entities to return per "page" of a collection. This is only used if the collection
returned is a `Laminas\Paginator\Paginator` instance or derivative.

##### Sub-key: `max_page_size` (optional)

The maximum number of entities to return per "page" of a collection.  This is tested against the
`page_size_param`. This parameter can be set to help prevent denial of service attacks against your API.

##### Sub-key: `min_page_size` (optional)

The minimum number of entities to return per "page" of a collection.  This is tested against the
`page_size_param`.

##### Sub-key: `page_size_param` (optional)

The name of a query string argument that will set a per-request page size. Not set by default; we
recommend having additional logic to ensure a ceiling for the page size as well, to prevent denial
of service attacks on your API.

#### User configuration example:

```php
'AddressBook\\V1\\Rest\\Contact\\Controller' => [
    'listener' => 'AddressBook\\V1\\Rest\\Contact\\ContactResource',
    'route_name' => 'address-book.rest.contact',
    'route_identifier_name' => 'contact_id',
    'collection_name' => 'contact',
    'entity_http_methods' => [
        0 => 'GET',
        1 => 'PATCH',
        2 => 'PUT',
        3 => 'DELETE',
    ],
    'collection_http_methods' => [
        0 => 'GET',
        1 => 'POST',
    ],
    'collection_query_whitelist' => [],
    'page_size' => 25,
    'page_size_param' => null,
    'entity_class' => 'AddressBook\\V1\\Rest\\Contact\\ContactEntity',
    'collection_class' => 'AddressBook\\V1\\Rest\\Contact\\ContactCollection',
    'service_name' => 'Contact',
],
```

### System Configuration

The `api-tools-rest` module provides the following configuration to ensure it operates properly in a Laminas
Framework application.

```php
'service_manager' => [
    'invokables' => [
        'Laminas\ApiTools\Rest\RestParametersListener' => 'Laminas\ApiTools\Rest\Listener\RestParametersListener',
    ],
    'factories' => [
        'Laminas\ApiTools\Rest\OptionsListener' => 'Laminas\ApiTools\Rest\Factory\OptionsListenerFactory',
    ],
],

'controllers' => [
    'abstract_factories' => [
        'Laminas\ApiTools\Rest\Factory\RestControllerFactory',
    ],
],

'view_manager' => [
    // Enable this in your application configuration in order to get full
    // exception stack traces in your API-Problem responses.
    'display_exceptions' => false,
],
```

Laminas Events
==========

### Listeners

#### Laminas\ApiTools\Rest\Listener\OptionsListener

This listener is registered to the `MvcEvent::EVENT_ROUTE` event with a priority of `-100`. 
It serves two purposes:

- If a request is made to either a REST entity or collection with a method they do not support, it
  will return a `405 Method not allowed` response, with a populated `Allow` header indicating which
  request methods may be used.
- For `OPTIONS` requests, it will respond with a `200 OK` response and a populated `Allow` header
  indicating which request methods may be used.

#### Laminas\ApiTools\Rest\Listener\RestParametersListener

This listener is attached to the shared `dispatch` event at priority `100`.  The listener maps query
string arguments from the request to the `Resource` object composed in the `RestController`, as well
as injects the `RouteMatch`.

Laminas Services
============

### Models

#### Laminas\ApiTools\Rest\AbstractResourceListener

This abstract class is the base implementation of a [Resource](#laminasrestresource) listener.  Since
dispatching of `api-tools-rest` based REST services is event driven, a listener must be constructed to
listen for events triggered from `Laminas\ApiTools\Rest\Resource` (which is called from the `RestController`).
The following methods are called during `dispatch()`, depending on the HTTP method:

- `create($data)` - Triggered by a `POST` request to a resource *collection*.
- `delete($id)` - Triggered by a `DELETE` request to a resource *entity*.
- `deleteList($data)` - Triggered by a `DELETE` request to a resource *collection*.
- `fetch($id)` - Triggered by a `GET` request to a resource *entity*.
- `fetchAll($params = [])` - Triggered by a `GET` request to a resource *collection*.
- `patch($id, $data)` - Triggered by a `PATCH` request to resource *entity*.
- `patchList($data)` - Triggered by a `PATCH` request to a resource *collection*.
- `update($id, $data)` - Triggered by a `PUT` request to a resource *entity*.
- `replaceList($data)` - Triggered by a `PUT` request to a resource *collection*.

#### Laminas\ApiTools\Rest\Resource

The `Resource` object handles dispatching business logic for REST requests. It composes an
`EventManager` instance in order to delegate operations to attached listeners. Additionally, it
composes request information, such as the `Request`, `RouteMatch`, and `MvcEvent` objects, in order
to seed the `ResourceEvent` it creates and passes to listeners when triggering events.

### Controller

#### Laminas\ApiTools\Rest\RestController

This is the base controller implementation used when a controller service name matches a configured
REST service. All REST services managed by `api-tools-rest` will use this controller (though separate
instances of it), unless they specify a [controller_class](#subkeycontrollerclassoptional) option.
Instances are created via the `Laminas\ApiTools\Rest\Factory\RestControllerFactory` abstract factory.

The `RestController` calls the appropriate method in `Laminas\ApiTools\Rest\Resource` based on the requested HTTP
method. It returns [HAL](https://github.com/laminas-api-tools/api-tools-hal) payloads on success, and [API
Problem](https://github.com/laminas-api-tools/api-tools-api-problem) responses on error.
