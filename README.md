<h4>CakePHP Component | EasyUploadComponent</h4>
<p>Componente para upload multiplo de imagens com associação de tabelas do banco</p>
<span>Componente em desenvolvimento, já supri as necessidades para qual foi desenvolvido, mas ainda não está concluído.</span>

<h5>Utilização</h5>
<p>Uma tabela de notícias e outra de fotos</p>
<h5>Controller</h5>
<pre>
<code>
public $components = array('EasyUpload');

$lastId 	= $this->Noticia->getLastInsertID();
$model 		= $this->Noticia->Foto;
$request 	= $this->request->data['Foto']['photo'];
$foreignKey = 'noticia_id';

$this->EasyUpload->uploader($model, $request, $lastId, $foreignKey);
</code>
</pre>

<h5>View</h5>
<p>O input para várias imagens</p>
<pre>
<code>
$this->Form->input('Foto.photo.', array('type' => 'file', 'multiple'));
</code>
</pre>
