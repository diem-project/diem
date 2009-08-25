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
 * Add tagging capabilities to your models
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
class Doctrine_Template_dmTaggableTag extends Doctrine_Template_TaggableTag
{
    public function getPopularTagsQueryTableProxy($relations = null, $limit = 10, $hydrationMode = Doctrine::HYDRATE_RECORD)
    {
        if ( ! $relations) {
            $relations = array();
            $allRelations = $this->getInvoker()->getTable()->getRelations();
            foreach ($allRelations as $name => $relation) {
                if ($relation['refTable']) {
                    $relations[] = $name;
                }
            }
        }
        $relations = (array) $relations;

        $q = $this->getInvoker()
            ->getTable()
            ->createQuery('t')
            ->select('t.*');

        $counts = array();
        foreach ($relations as $relation) {
            $countAlias = 'num_' . Doctrine_Inflector::tableize($relation);

            $q->leftJoin('t.' . $relation . ' '.$relation);
            $q->addSelect('COUNT(DISTINCT ' . $relation . '.id) AS ' . $countAlias);
            $counts[] = 'COUNT(DISTINCT ' . $relation .'.id)';
        }

        $q->addSelect('(' . implode(' + ', $counts) . ') as total_num');
        $q->orderBy('total_num DESC');
        $q->groupBy('t.id');
        $q->addHaving('total_num > 0');
        $q->limit($limit);

        return $q;
    }

    public function getPopularTagsTableProxy($relations = null, $limit = 10, $hydrationMode = Doctrine::HYDRATE_RECORD)
    {
        $q = $this->getPopularTagsQueryTableProxy($relations, $limit, $hydrationMode);

        return $q->execute(array(), $hydrationMode);
    }

    public function getPopularTagsArrayTableProxy($relations = null, $limit = 10)
    {
        $popularTags = $this->getPopularTagsTableProxy($relations, $limit, Doctrine::HYDRATE_ARRAY);
        foreach ($popularTags as $tag => $info) {
            $popularTags[$tag] = $info['total_num'];
        }
        ksort($popularTags);
        return $popularTags;
    }
}