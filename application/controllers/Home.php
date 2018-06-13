<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Home extends CI_Controller {

	/**
	 * Index Page for this controller.
	 *
	 * Maps to the following URL
	 * 		http://example.com/index.php/welcome
	 *	- or -
	 * 		http://example.com/index.php/welcome/index
	 *	- or -
	 * Since this controller is set as the default controller in
	 * config/routes.php, it's displayed at http://example.com/
	 *
	 * So any other public methods not prefixed with an underscore will
	 * map to /index.php/welcome/<method_name>
	 * @see https://codeigniter.com/user_guide/general/urls.html
	 */
	public function index()
	{
		$produtos = array(
			(object) array(
				'id' => 1,
				'descricao' => 'Produto de Teste 1',
				'preco' => '1.99'
			),
			(object) array(
				'id' => 2,
				'descricao' => 'Produto de Teste 2',
				'preco' => '2.99'
			),
			(object) array(
				'id' => 3,
				'descricao' => 'Produto de Teste 3',
				'preco' => '3.99'
			)
		);

		$this->load->view('home', compact('produtos'));
	}

	public function comprar()
	{
		// recebe os valores via POST e monta um array
		
		$produtos = array(
			(object) array(
				'id' => $this->input->post('id'),
				'descricao' => $this->input->post('descricao'),
				'preco' => $this->input->post('preco')
			)
		);

		// monta o array
		$data = array(
			'descricao' => $produtos[0]->descricao,
			'preco' => $produtos[0]->preco
		);
		// insere o Pedido
		$this->db->insert('pedidos', $data);
		
		// retorna o ID do Pedido
		$pedido_id = $this->db->insert_id();

		// retorna o codigo
		echo $this->get_codigo_pagamento($produtos, $pedido_id);
	}

	// função para listar os pedidos
	public function pedidos()
	{
		$pedidos = $this->db->select('p.id, p.descricao, p.preco, s.status')
		->join('status_pedido s ', 's.id = p.status')
		->get('pedidos p')->result();

		$this->load->view('pedidos', compact('pedidos'));
	}

	// função para atualizar o status do pedido pelo PagSeguro
	public function notificacao()
	{
		$notificationCode = $this->input->post('notificationCode');

		// campos default
		// $data['token'] ='seu_token'; // sandbox
		// $data['email'] = 'seu_email'; // sandbox
		
		$data['token'] ='seu_token'; // produção
		$data['email'] = 'seu_email'; // produção

		$data = http_build_query($data);

		// $url = 'https://ws.sandbox.pagseguro.uol.com.br/v3/transactions/notifications/'.$notificationCode.'?'.$data; // sandbox
		$url = 'https://ws.pagseguro.uol.com.br/v3/transactions/notifications/'.$notificationCode.'?'.$data; // produção

		$curl = curl_init();
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl, CURLOPT_URL, $url);
		$xml = curl_exec($curl);
		curl_close($curl);

		$xml = simplexml_load_string($xml);

		$reference = $xml->reference;
		$status = $xml->status;

		if($reference && $status){
			// consulta se existe o pedido pela referencia
			$rs_pedido = $this->consultar_pedido($reference);

			if($rs_pedido){
				// atualiza o pedido
				$this->atualiza_pedido($reference, $status);
			}
		}
	}

	// função para gerar o codigo de transação do pagseguro
	private function get_codigo_pagamento($produtos = null, $reference = null)
	{
		// campos default
		// $data['token'] ='seu_token'; // sandbox
		// $data['email'] = 'seu_email'; // sandbox
		
		$data['token'] ='seu_token'; // produção
		$data['email'] = 'seu_email'; // produção
		
		$data['currency'] = 'BRL';
		$data['reference'] = $reference;

		// controle
		$i = 1;
		
		// loop com os produtos
		foreach ($produtos as $p) {
			$data['itemId'.$i] = $p->id;
			$data['itemQuantity'.$i] = '1';
			$data['itemDescription'.$i] = $p->descricao;
			$data['itemAmount'.$i] = $p->preco;

			$i++;
		}


		// $url = 'https://ws.sandbox.pagseguro.uol.com.br/v2/checkout'; // sandbox
		$url = 'https://ws.pagseguro.uol.com.br/v2/checkout'; // produção

		$data = http_build_query($data);

		$curl = curl_init($url);

		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($curl, CURLOPT_POST, true);
		curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
		curl_setopt($curl, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);

		$xml= curl_exec($curl);

		curl_close($curl);

		$xml = simplexml_load_string($xml);
		
		return $xml->code;
	}

	// função para consultar se existe o pedido
	private function consultar_pedido($reference = null)
	{
		return $this->db->get_where('pedidos', array('id' => $reference))->row();
	}

	// função para atualizar o status do pedido
	private function atualiza_pedido($reference = null, $status = null)
	{
		$this->db->set('status', $status)
		->where('id', $reference)
		->update('pedidos');
	}
}

