<?php
/*
 *  $Id$
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS
 * "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT
 * LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR
 * A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT
 * OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL,
 * SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT
 * LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE,
 * DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY
 * THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
 * (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE
 * OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 *
 * This software consists of voluntary contributions made by many individuals
 * and is licensed under the LGPL. For more information, see
 * <http://www.phpdoctrine.org>.
 */

/**
 * Behavior for adding Tagging features to your models
 *
 * @package     Doctrine
 * @subpackage  Template
 * @license     http://www.opensource.org/licenses/lgpl-license.php LGPL
 * @link        www.phpdoctrine.org
 * @since       1.0
 * @version     $Revision$
 * @author      Konsta Vesterinen <kvesteri@cc.hut.fi>
 * @author      Jonathan H. Wage <jonwage@gmail.com>
 */
class Doctrine_dmTaggable extends Doctrine_Taggable
{

	public function __construct(array $options = array())
	{
		$this->_options = $options;

		$this->_options['generateFiles']  = true;
		$this->_options['generatePath']   = sfConfig::get('sf_lib_dir').'/model/doctrine';
		$this->_options['builderOptions'] = sfConfig::get('doctrine_model_builder_options');
	}

	public function setUp()
	{
		$tag = new Doctrine_dmTaggable_Tag();
		$tag->setOption('parent', $this);
		$tag->setOption('tagField', $this->_options['tagField']);
		$this->addChild($tag);
	}


	public function buildTable()
	{
		// Bind model
		$conn = $this->_options['table']->getConnection();
		$conn->getManager()->bindComponent($this->_options['className'], $conn->getName());

		// Create table
		$this->_table = new myDoctrineTable($this->_options['className'], $conn);

		// If custom table name set then lets use it
		if (isset($this->_options['tableName']) && $this->_options['tableName']) {
			$this->_table->setTableName($this->_options['tableName']);
		}

		// Maintain some options from the parent table
		$options = $this->_options['table']->getOptions();

		$newOptions = array();
		$maintain = array('type', 'collate', 'charset'); // This list may need updating
		foreach ($maintain as $key) {
			if (isset($options[$key])) {
				$newOptions[$key] = $options[$key];
			}
		}

		$this->_table->setOptions($newOptions);

		$conn->addTable($this->_table);
	}
}