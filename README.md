# GForm

## Config
### Inject Provider
config/app.php<br/>
<b>Provider</b><br/>
Collective\Html\HtmlServiceProvider::class<br/>
GCollection\GForm\GFormServiceProvider::class<br/>
<br/>
<b>Alias</b><br/>
'Html' => Collective\Html\HtmlFacade::class<br/>
 'Form' => Collective\Html\FormFacade::class<br/>
'GForm' => GCollection\GForm\Facades\GFormFacade::class<br/>
