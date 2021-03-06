<?php

/**
 * This file is part of the Nette Framework (http://nette.org)
 *
 * Copyright (c) 2004 David Grudl (http://davidgrudl.com)
 *
 * For the full copyright and license information, please view
 * the file license.txt that was distributed with this source code.
 * @package Nette\Loaders
 */



/**
 * Nette auto loader is responsible for loading Nette classes and interfaces.
 *
 * @author     David Grudl
 * @package Nette\Loaders
 */
class NNetteLoader extends NAutoLoader
{
	/** @var NNetteLoader */
	private static $instance;

	/** @var array */
	public $renamed = array(
		'NConfigurator' => 'NConfigurator',
		'NUser' => 'NUser',
		'NDefaultHelpers' => 'NTemplateHelpers',
		'NLatteException' => 'NCompileException',
	);

	/** @var array */
	public $list = array(
		'ArgumentOutOfRangeException' => '/common/exceptions',
		'DeprecatedException' => '/common/exceptions',
		'DirectoryNotFoundException' => '/common/exceptions',
		'FatalErrorException' => '/common/exceptions',
		'FileNotFoundException' => '/common/exceptions',
		'IAnnotation' => '/Reflection/IAnnotation',
		'IAuthenticator' => '/Security/IAuthenticator',
		'IAuthorizator' => '/Security/IAuthorizator',
		'IBarPanel' => '/Diagnostics/IBarPanel',
		'ICacheJournal' => '/Caching/Storages/IJournal',
		'ICacheStorage' => '/Caching/IStorage',
		'IComponent' => '/ComponentModel/IComponent',
		'IComponentContainer' => '/ComponentModel/IContainer',
		'IConfigAdapter' => '/Config/IAdapter',
		'IDIContainer' => '/DI/IContainer',
		'IFileTemplate' => '/Templating/IFileTemplate',
		'IFormControl' => '/Forms/IControl',
		'IFormRenderer' => '/Forms/IFormRenderer',
		'IFreezable' => '/common/IFreezable',
		'IHttpRequest' => '/Http/IRequest',
		'IHttpResponse' => '/Http/IResponse',
		'IIdentity' => '/Security/IIdentity',
		'IMacro' => '/Latte/IMacro',
		'IMailer' => '/Mail/IMailer',
		'IOException' => '/common/exceptions',
		'IPresenter' => '/Application/IPresenter',
		'IPresenterFactory' => '/Application/IPresenterFactory',
		'IPresenterResponse' => '/Application/IResponse',
		'IReflection' => '/Database/IReflection',
		'IRenderable' => '/Application/UI/IRenderable',
		'IResource' => '/Security/IResource',
		'IRole' => '/Security/IRole',
		'IRouter' => '/Application/IRouter',
		'ISessionStorage' => '/Http/ISessionStorage',
		'ISignalReceiver' => '/Application/UI/ISignalReceiver',
		'IStatePersistent' => '/Application/UI/IStatePersistent',
		'ISubmitterControl' => '/Forms/ISubmitterControl',
		'ISupplementalDriver' => '/Database/ISupplementalDriver',
		'ITemplate' => '/Templating/ITemplate',
		'ITranslator' => '/Localization/ITranslator',
		'IUserStorage' => '/Security/IUserStorage',
		'InvalidStateException' => '/common/exceptions',
		'MemberAccessException' => '/common/exceptions',
		'MicroPresenter' => '/Application/MicroPresenter',
		'NAbortException' => '/Application/exceptions',
		'NAnnotation' => '/Reflection/Annotation',
		'NAnnotationsParser' => '/Reflection/AnnotationsParser',
		'NAppForm' => '/Application/UI/Form',
		'NApplication' => '/Application/Application',
		'NApplicationException' => '/Application/exceptions',
		'NArrayHash' => '/common/ArrayHash',
		'NArrayList' => '/common/ArrayList',
		'NArrays' => '/Utils/Arrays',
		'NAssertionException' => '/Utils/Validators',
		'NAuthenticationException' => '/Security/AuthenticationException',
		'NAutoLoader' => '/Loaders/AutoLoader',
		'NBadRequestException' => '/Application/exceptions',
		'NBadSignalException' => '/Application/UI/BadSignalException',
		'NButton' => '/Forms/Controls/Button',
		'NCFix' => '/loader',
		'NCache' => '/Caching/Cache',
		'NCacheMacro' => '/Latte/Macros/CacheMacro',
		'NCachingHelper' => '/Caching/OutputHelper',
		'NCallback' => '/common/Callback',
		'NCheckbox' => '/Forms/Controls/Checkbox',
		'NClassReflection' => '/Reflection/ClassType',
		'NCliRouter' => '/Application/Routers/CliRouter',
		'NCompileException' => '/Latte/exceptions',
		'NComponent' => '/ComponentModel/Component',
		'NComponentContainer' => '/ComponentModel/Container',
		'NConfigCompiler' => '/Config/Compiler',
		'NConfigCompilerExtension' => '/Config/CompilerExtension',
		'NConfigHelpers' => '/Config/Helpers',
		'NConfigIniAdapter' => '/Config/Adapters/IniAdapter',
		'NConfigLoader' => '/Config/Loader',
		'NConfigNeonAdapter' => '/Config/Adapters/NeonAdapter',
		'NConfigPhpAdapter' => '/Config/Adapters/PhpAdapter',
		'NConfigurator' => '/Config/Configurator',
		'NConnection' => '/Database/Connection',
		'NConstantsExtension' => '/Config/Extensions/ConstantsExtension',
		'NContainerPanel' => '/DI/Diagnostics/ContainerPanel',
		'NControl' => '/Application/UI/Control',
		'NConventionalReflection' => '/Database/Reflection/ConventionalReflection',
		'NCoreMacros' => '/Latte/Macros/CoreMacros',
		'NDIContainer' => '/DI/Container',
		'NDIContainerBuilder' => '/DI/ContainerBuilder',
		'NDIHelpers' => '/DI/Helpers',
		'NDINestedAccessor' => '/DI/NestedAccessor',
		'NDIServiceDefinition' => '/DI/ServiceDefinition',
		'NDIStatement' => '/DI/Statement',
		'NDatabaseHelpers' => '/Database/Helpers',
		'NDatabasePanel' => '/Database/Diagnostics/ConnectionPanel',
		'NDateTime53' => '/common/DateTime',
		'NDebugBar' => '/Diagnostics/Bar',
		'NDebugBlueScreen' => '/Diagnostics/BlueScreen',
		'NDebugHelpers' => '/Diagnostics/Helpers',
		'NDebugger' => '/Diagnostics/Debugger',
		'NDefaultBarPanel' => '/Diagnostics/DefaultBarPanel',
		'NDefaultFormRenderer' => '/Forms/Rendering/DefaultFormRenderer',
		'NDevNullStorage' => '/Caching/Storages/DevNullStorage',
		'NDiscoveredReflection' => '/Database/Reflection/DiscoveredReflection',
		'NEnvironment' => '/common/Environment',
		'NExtensionReflection' => '/Reflection/Extension',
		'NFileJournal' => '/Caching/Storages/FileJournal',
		'NFileResponse' => '/Application/Responses/FileResponse',
		'NFileStorage' => '/Caching/Storages/FileStorage',
		'NFileTemplate' => '/Templating/FileTemplate',
		'NFinder' => '/Utils/Finder',
		'NFireLogger' => '/Diagnostics/FireLogger',
		'NForbiddenRequestException' => '/Application/exceptions',
		'NForm' => '/Forms/Form',
		'NFormContainer' => '/Forms/Container',
		'NFormControl' => '/Forms/Controls/BaseControl',
		'NFormGroup' => '/Forms/ControlGroup',
		'NFormMacros' => '/Latte/Macros/FormMacros',
		'NForwardResponse' => '/Application/Responses/ForwardResponse',
		'NFramework' => '/common/Framework',
		'NFreezableObject' => '/common/FreezableObject',
		'NFunctionReflection' => '/Reflection/GlobalFunction',
		'NGenericRecursiveIterator' => '/Iterators/Recursor',
		'NGroupedTableSelection' => '/Database/Table/GroupedSelection',
		'NHiddenField' => '/Forms/Controls/HiddenField',
		'NHtml' => '/Utils/Html',
		'NHtmlNode' => '/Latte/HtmlNode',
		'NHttpContext' => '/Http/Context',
		'NHttpRequest' => '/Http/Request',
		'NHttpRequestFactory' => '/Http/RequestFactory',
		'NHttpResponse' => '/Http/Response',
		'NHttpUploadedFile' => '/Http/FileUpload',
		'NIdentity' => '/Security/Identity',
		'NImage' => '/common/Image',
		'NImageButton' => '/Forms/Controls/ImageButton',
		'NInstanceFilterIterator' => '/Iterators/InstanceFilter',
		'NInvalidLinkException' => '/Application/UI/InvalidLinkException',
		'NInvalidPresenterException' => '/Application/exceptions',
		'NJson' => '/Utils/Json',
		'NJsonException' => '/Utils/Json',
		'NJsonResponse' => '/Application/Responses/JsonResponse',
		'NLatteCompiler' => '/Latte/Compiler',
		'NLatteFilter' => '/Latte/Engine',
		'NLatteToken' => '/Latte/Token',
		'NLimitedScope' => '/Utils/LimitedScope',
		'NLink' => '/Application/UI/Link',
		'NLogger' => '/Diagnostics/Logger',
		'NMacroNode' => '/Latte/MacroNode',
		'NMacroSet' => '/Latte/Macros/MacroSet',
		'NMacroTokenizer' => '/Latte/MacroTokenizer',
		'NMail' => '/Mail/Message',
		'NMailMimePart' => '/Mail/MimePart',
		'NMapIterator' => '/Iterators/Mapper',
		'NMemcachedStorage' => '/Caching/Storages/MemcachedStorage',
		'NMemoryStorage' => '/Caching/Storages/MemoryStorage',
		'NMethodReflection' => '/Reflection/Method',
		'NMimeTypeDetector' => '/Utils/MimeTypeDetector',
		'NMissingServiceException' => '/DI/exceptions',
		'NMsSqlDriver' => '/Database/Drivers/MsSqlDriver',
		'NMultiSelectBox' => '/Forms/Controls/MultiSelectBox',
		'NMultiplier' => '/Application/UI/Multiplier',
		'NMySqlDriver' => '/Database/Drivers/MySqlDriver',
		'NNCallbackFilterIterator' => '/Iterators/Filter',
		'NNRecursiveCallbackFilterIterator' => '/Iterators/RecursiveFilter',
		'NNeon' => '/Utils/Neon',
		'NNeonEntity' => '/Utils/Neon',
		'NNeonException' => '/Utils/Neon',
		'NNetteExtension' => '/Config/Extensions/NetteExtension',
		'NNetteLoader' => '/Loaders/NetteLoader',
		'NObject' => '/common/Object',
		'NObjectMixin' => '/common/ObjectMixin',
		'NOciDriver' => '/Database/Drivers/OciDriver',
		'NOdbcDriver' => '/Database/Drivers/OdbcDriver',
		'NPaginator' => '/Utils/Paginator',
		'NParameterReflection' => '/Reflection/Parameter',
		'NParser' => '/Latte/Parser',
		'NPermission' => '/Security/Permission',
		'NPgSqlDriver' => '/Database/Drivers/PgSqlDriver',
		'NPhpClassType' => '/Utils/PhpGenerator/ClassType',
		'NPhpExtension' => '/Config/Extensions/PhpExtension',
		'NPhpFileStorage' => '/Caching/Storages/PhpFileStorage',
		'NPhpHelpers' => '/Utils/PhpGenerator/Helpers',
		'NPhpLiteral' => '/Utils/PhpGenerator/PhpLiteral',
		'NPhpMethod' => '/Utils/PhpGenerator/Method',
		'NPhpParameter' => '/Utils/PhpGenerator/Parameter',
		'NPhpProperty' => '/Utils/PhpGenerator/Property',
		'NPhpWriter' => '/Latte/PhpWriter',
		'NPresenter' => '/Application/UI/Presenter',
		'NPresenterComponent' => '/Application/UI/PresenterComponent',
		'NPresenterComponentReflection' => '/Application/UI/PresenterComponentReflection',
		'NPresenterFactory' => '/Application/PresenterFactory',
		'NPresenterRequest' => '/Application/Request',
		'NPropertyReflection' => '/Reflection/Property',
		'NRadioList' => '/Forms/Controls/RadioList',
		'NRecursiveComponentIterator' => '/ComponentModel/RecursiveComponentIterator',
		'NRedirectResponse' => '/Application/Responses/RedirectResponse',
		'NRegexpException' => '/Utils/Strings',
		'NRobotLoader' => '/Loaders/RobotLoader',
		'NRoute' => '/Application/Routers/Route',
		'NRouteList' => '/Application/Routers/RouteList',
		'NRoutingDebugger' => '/Application/Diagnostics/RoutingPanel',
		'NRow' => '/Database/Row',
		'NRule' => '/Forms/Rule',
		'NRules' => '/Forms/Rules',
		'NSafeStream' => '/Utils/SafeStream',
		'NSelectBox' => '/Forms/Controls/SelectBox',
		'NSendmailMailer' => '/Mail/SendmailMailer',
		'NServiceCreationException' => '/DI/exceptions',
		'NSession' => '/Http/Session',
		'NSessionSection' => '/Http/SessionSection',
		'NSimpleAuthenticator' => '/Security/SimpleAuthenticator',
		'NSimpleRouter' => '/Application/Routers/SimpleRouter',
		'NSmartCachingIterator' => '/Iterators/CachingIterator',
		'NSmtpException' => '/Mail/SmtpMailer',
		'NSmtpMailer' => '/Mail/SmtpMailer',
		'NSqlLiteral' => '/Database/SqlLiteral',
		'NSqlPreprocessor' => '/Database/SqlPreprocessor',
		'NSqlite2Driver' => '/Database/Drivers/Sqlite2Driver',
		'NSqliteDriver' => '/Database/Drivers/SqliteDriver',
		'NStatement' => '/Database/Statement',
		'NStaticClassException' => '/common/exceptions',
		'NStrings' => '/Utils/Strings',
		'NSubmitButton' => '/Forms/Controls/SubmitButton',
		'NTableRow' => '/Database/Table/ActiveRow',
		'NTableSelection' => '/Database/Table/Selection',
		'NTemplate' => '/Templating/Template',
		'NTemplateException' => '/Templating/FilterException',
		'NTemplateHelpers' => '/Templating/Helpers',
		'NTextArea' => '/Forms/Controls/TextArea',
		'NTextBase' => '/Forms/Controls/TextBase',
		'NTextInput' => '/Forms/Controls/TextInput',
		'NTextResponse' => '/Application/Responses/TextResponse',
		'NTokenizer' => '/Utils/Tokenizer',
		'NTokenizerException' => '/Utils/Tokenizer',
		'NUIMacros' => '/Latte/Macros/UIMacros',
		'NUnknownImageFileException' => '/common/Image',
		'NUploadControl' => '/Forms/Controls/UploadControl',
		'NUrl' => '/Http/Url',
		'NUrlScript' => '/Http/UrlScript',
		'NUser' => '/Security/User',
		'NUserPanel' => '/Security/Diagnostics/UserPanel',
		'NUserStorage' => '/Http/UserStorage',
		'NValidators' => '/Utils/Validators',
		'NotImplementedException' => '/common/exceptions',
		'NotSupportedException' => '/common/exceptions',
	);



	/**
	 * Returns singleton instance with lazy instantiation.
	 * @return NNetteLoader
	 */
	public static function getInstance()
	{
		if (self::$instance === NULL) {
			self::$instance = new self;
		}
		return self::$instance;
	}



	/**
	 * Handles autoloading of classes or interfaces.
	 * @param  string
	 * @return void
	 */
	public function tryLoad($type)
	{
		$type = ltrim($type, '\\');
		if (isset($this->list[$type])) {
			NLimitedScope::load(NETTE_DIR . $this->list[$type] . '.php', TRUE);
			self::$count++;

		}}

}
