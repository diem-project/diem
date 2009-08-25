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
 * Doctrine_Record_Generator
 *
 * @author      Konsta Vesterinen <kvesteri@cc.hut.fi>
 * @package     Doctrine
 * @subpackage  Plugin
 * @license     http://www.opensource.org/licenses/lgpl-license.php LGPL
 * @version     $Revision$
 * @link        www.phpdoctrine.org
 * @since       1.0
 */
abstract class dmDoctrineRecordGenerator extends Doctrine_Record_Generator
{
	/*
	 * HACK
	 * replaced Doctrine_Table by myDoctrineTable
	 */
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