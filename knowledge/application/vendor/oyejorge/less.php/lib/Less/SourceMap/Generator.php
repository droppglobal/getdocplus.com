<?php

/**
 * Source map generator
 *
 * @package Less
 * @subpackage Output
 */
class Less_SourceMap_Generator extends Less_Configurable {

	/**
	 * What version of source map does the generator generate?
	 */
	const VERSION = 3;

	/**
	 * Array of default options
	 *
	 * @var array
	 */
	protected $defaultOptions = array(
												'sourceRoot'			=> '',

						'sourceMapFilename'		=> null,

						'sourceMapURL'			=> null,

						'sourceMapWriteTo'		=> null,

						'outputSourceFiles'		=> false,

						'sourceMapRootpath'		=> '',

						'sourceMapBasepath'   => ''
	);

	/**
	 * The base64 VLQ encoder
	 *
	 * @var Less_SourceMap_Base64VLQ
	 */
	protected $encoder;

	/**
	 * Array of mappings
	 *
	 * @var array
	 */
	protected $mappings = array();

	/**
	 * The root node
	 *
	 * @var Less_Tree_Ruleset
	 */
	protected $root;

	/**
	 * Array of contents map
	 *
	 * @var array
	 */
	protected $contentsMap = array();

	/**
	 * File to content map
	 *
	 * @var array
	 */
	protected $sources = array();
	protected $source_keys = array();

	/**
	 * Constructor
	 *
	 * @param Less_Tree_Ruleset $root The root node
	 * @param array $options Array of options
	 */
	public function __construct(Less_Tree_Ruleset $root, $contentsMap, $options = array()){
		$this->root = $root;
		$this->contentsMap = $contentsMap;
		$this->encoder = new Less_SourceMap_Base64VLQ();

		$this->SetOptions($options);
		
		$this->options['sourceMapRootpath'] = $this->fixWindowsPath($this->options['sourceMapRootpath'], true);
		$this->options['sourceMapBasepath'] = $this->fixWindowsPath($this->options['sourceMapBasepath'], true);
	}

	/**
	 * Generates the CSS
	 *
	 * @return string
	 */
	public function generateCSS(){
		$output = new Less_Output_Mapped($this->contentsMap, $this);

				$this->root->genCSS($output);


		$sourceMapUrl				= $this->getOption('sourceMapURL');
		$sourceMapFilename			= $this->getOption('sourceMapFilename');
		$sourceMapContent			= $this->generateJson();
		$sourceMapWriteTo			= $this->getOption('sourceMapWriteTo');

		if( !$sourceMapUrl && $sourceMapFilename ){
			$sourceMapUrl = $this->normalizeFilename($sourceMapFilename);
		}

				if( $sourceMapWriteTo ){
			$this->saveMap($sourceMapWriteTo, $sourceMapContent);
		}

				if( !$sourceMapUrl ){
			$sourceMapUrl = sprintf('data:application/json,%s', Less_Functions::encodeURIComponent($sourceMapContent));
		}

		if( $sourceMapUrl ){
			$output->add( sprintf('/*# sourceMappingURL=%s */', $sourceMapUrl) );
		}

		return $output->toString();
	}

	/**
	 * Saves the source map to a file
	 *
	 * @param string $file The absolute path to a file
	 * @param string $content The content to write
	 * @throws Exception If the file could not be saved
	 */
	protected function saveMap($file, $content){
		$dir = dirname($file);
				if( !is_dir($dir) ){
						throw new Exception(sprintf('The directory "%s" does not exist. Cannot save the source map.', $dir));
		}
				if(file_put_contents($file, $content) === false){
			throw new Exception(sprintf('Cannot save the source map to "%s"', $file));
		}
		return true;
	}

	/**
	 * Normalizes the filename
	 *
	 * @param string $filename
	 * @return string
	 */
	protected function normalizeFilename($filename){

		$filename = $this->fixWindowsPath($filename);

		$rootpath = $this->getOption('sourceMapRootpath');
		$basePath = $this->getOption('sourceMapBasepath');

				if (strpos($filename, $basePath) === 0) {
			$filename = substr($filename, strlen($basePath));
		}

				if(strpos($filename, '\\') === 0 || strpos($filename, '/') === 0){
			$filename = substr($filename, 1);
		}

		return $rootpath . $filename;
	}

	/**
	 * Adds a mapping
	 *
	 * @param integer $generatedLine The line number in generated file
	 * @param integer $generatedColumn The column number in generated file
	 * @param integer $originalLine The line number in original file
	 * @param integer $originalColumn The column number in original file
	 * @param string $sourceFile The original source file
	 */
	public function addMapping($generatedLine, $generatedColumn, $originalLine, $originalColumn, $fileInfo ){

		$this->mappings[] = array(
			'generated_line' => $generatedLine,
			'generated_column' => $generatedColumn,
			'original_line' => $originalLine,
			'original_column' => $originalColumn,
			'source_file' => $fileInfo['currentUri']
		);

		$this->sources[$fileInfo['currentUri']] = $fileInfo['filename'];
	}


	/**
	 * Generates the JSON source map
	 *
	 * @return string
	 * @see https://docs.google.com/document/d/1U1RGAehQwRypUTovF1KRlpiOFze0b-_2gc6fAH0KY0k/edit#
	 */
	protected function generateJson(){

		$sourceMap = array();
		$mappings = $this->generateMappings();

				$sourceMap['version'] = self::VERSION;


				$file = $this->getOption('sourceMapFilename');
		if( $file ){
			$sourceMap['file'] = $file;
		}


				$root = $this->getOption('sourceRoot');
		if( $root ){
			$sourceMap['sourceRoot'] = $root;
		}


				$sourceMap['sources'] = array();
		foreach($this->sources as $source_uri => $source_filename){
			$sourceMap['sources'][] = $this->normalizeFilename($source_filename);
		}


				$sourceMap['names'] = array();

				$sourceMap['mappings'] = $mappings;

		if( $this->getOption('outputSourceFiles') ){
												$sourceMap['sourcesContent'] = $this->getSourcesContent();
		}

				if( count($sourceMap['sources']) && empty($sourceMap['sourceRoot']) ){
			unset($sourceMap['sourceRoot']);
		}

		return json_encode($sourceMap);
	}

	/**
	 * Returns the sources contents
	 *
	 * @return array|null
	 */
	protected function getSourcesContent(){
		if(empty($this->sources)){
			return;
		}
		$content = array();
		foreach($this->sources as $sourceFile){
			$content[] = file_get_contents($sourceFile);
		}
		return $content;
	}

	/**
	 * Generates the mappings string
	 *
	 * @return string
	 */
	public function generateMappings(){

		if( !count($this->mappings) ){
			return '';
		}

		$this->source_keys = array_flip(array_keys($this->sources));


				$groupedMap = $groupedMapEncoded = array();
		foreach($this->mappings as $m){
			$groupedMap[$m['generated_line']][] = $m;
		}
		ksort($groupedMap);

		$lastGeneratedLine = $lastOriginalIndex = $lastOriginalLine = $lastOriginalColumn = 0;

		foreach($groupedMap as $lineNumber => $line_map){
			while(++$lastGeneratedLine < $lineNumber){
				$groupedMapEncoded[] = ';';
			}

			$lineMapEncoded = array();
			$lastGeneratedColumn = 0;

			foreach($line_map as $m){
				$mapEncoded = $this->encoder->encode($m['generated_column'] - $lastGeneratedColumn);
				$lastGeneratedColumn = $m['generated_column'];

								if( $m['source_file'] ){
					$index = $this->findFileIndex($m['source_file']);
					if( $index !== false ){
						$mapEncoded .= $this->encoder->encode($index - $lastOriginalIndex);
						$lastOriginalIndex = $index;

												$mapEncoded .= $this->encoder->encode($m['original_line'] - 1 - $lastOriginalLine);
						$lastOriginalLine = $m['original_line'] - 1;

						$mapEncoded .= $this->encoder->encode($m['original_column'] - $lastOriginalColumn);
						$lastOriginalColumn = $m['original_column'];
					}
				}

				$lineMapEncoded[] = $mapEncoded;
			}

			$groupedMapEncoded[] = implode(',', $lineMapEncoded) . ';';
		}

		return rtrim(implode($groupedMapEncoded), ';');
	}

	/**
	 * Finds the index for the filename
	 *
	 * @param string $filename
	 * @return integer|false
	 */
	protected function findFileIndex($filename){
		return $this->source_keys[$filename];
	}

	/**
	 * fix windows paths
	 * @param  string $path
	 * @return string      
	 */
	public function fixWindowsPath($path, $addEndSlash = false){
		$slash = ($addEndSlash) ? '/' : '';
		if( !empty($path) ){
			$path = str_replace('\\', '/', $path);
			$path = rtrim($path,'/') . $slash;
		}

		return $path;
	}

}