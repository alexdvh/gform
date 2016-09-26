# HForm

## Config
### Inject Provider
config/app.php
Provider
Collective\Html\HtmlServiceProvider::class,
HCollection\HForm\HFormServiceProvider::class,

Alias
'Html' => Collective\Html\HtmlFacade::class,
 'Form' => Collective\Html\FormFacade::class,
'HForm' => HCollection\HForm\Facades\HFormFacade::class,
