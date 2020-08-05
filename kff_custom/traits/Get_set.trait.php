<?php

/**
 *
 */
trait Get_set
{
	protected $data = [];

	public function get($pname)
	{
		return $this->data[$pname] ?? null;
	}

	public function __get($pname)
	{
		return $this->{$pname} ?? $this->get($pname);
	}

	protected function set($pname, $pval)
	{
		return $this->data[$pname] = $pval;
	}

	/* protected function __set($pname, $pval)
	{
		return $this->data[$pname] = $pval;
	} */
}
