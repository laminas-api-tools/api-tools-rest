.. _basics.index:

LaminasRest Basics
=============

LaminasRest allows you to create RESTful JSON APIs that adhere to
:ref:`Hypermedia Application Language <laminasrest.hal-primer>`. For error
handling, it uses :ref:`API-Problem <laminasrest.error-reporting>`.

The pieces you need to implement, work with, or understand are:

- Writing event listeners for the various ``Laminas\ApiTools\Rest\Resource`` events,
  which will be used to either persist resources or fetch resources from
  persistence.

- Writing routes for your resources, and associating them with resources and/or
  ``Laminas\ApiTools\Rest\ResourceController``.

- Writing metadata describing your resources, including what routes to associate
  with them.

All API calls are handled by ``Laminas\ApiTools\Rest\ResourceController``, which in
turn composes a ``Laminas\ApiTools\Rest\Resource`` object and calls methods on it. The
various methods of the controller will return either
``Laminas\ApiTools\Rest\ApiProblem`` results on error conditions, or, on success, a
``Laminas\ApiTools\Rest\HalResource`` or ``Laminas\ApiTools\Rest\HalCollection`` instance; these
are then composed into a ``Laminas\ApiTools\Rest\View\RestfulJsonModel``.

If the MVC detects a ``Laminas\ApiTools\Rest\View\RestfulJsonModel`` during rendering,
it will select ``Laminas\ApiTools\Rest\View\RestfulJsonRenderer``. This, with the help
of the ``Laminas\ApiTools\Rest\Plugin\HalLinks`` plugin, will generate an appropriate
payload based on the object composed, and ensure the appropriate Content-Type
header is used.

If a ``Laminas\ApiTools\Rest\HalCollection`` is detected, and the renderer determines
that it composes a ``Laminas\Paginator\Paginator`` instance, the ``HalLinks``
plugin will also generate pagination relational links to render in the payload.
