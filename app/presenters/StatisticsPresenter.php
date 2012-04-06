<?php

/**
 * Statistics presenter.
 *
 * @author     Samuel Kelemen
 */
class StatisticsPresenter extends BaseLPresenter
{
	public $colors = array(
		'#305abc',	
		'#c3000e',	
		'#32c300',	
		'#6f24cc',	
		'#e07432',	
		'#8cd108',	
		'#b810cc',
		'#397d99',
		'#de5909',
		'#920156',
		'#530192',
		'#014592',
		'#459201',
		'#cf39d4',
		'#3982d4',
		'#39d485',
		'#b45e3c',
		'#3c74b4',
		'#6c3cb4',
		'#b43c6f',
		'#b43c3c',
		'#91b43c',
		'#e885a3',
		'#e8c185',
		'#8985e8',
	);
	
	
	
	public function renderDefault() {
		$faculties = $this->db->table('faculty');
		$this->template->faculties = $faculties;
		
		$school = array();
		foreach($faculties as $faculty) {
			$school[$faculty->id]['money'] = 0;
			$school[$faculty->id]['students'] = 0;
			$school[$faculty->id]['acronym'] = $faculty->acronym;
			foreach($faculty->related('institute') as $institute) {
				$school[$faculty->id]['money'] += $institute->money;
				$school[$faculty->id]['students'] += $institute->students;
			}
		}
		
		rsort($school);
		
		$this->template->school = $school;
		$this->template->colors = $this->colors;
	}
}
