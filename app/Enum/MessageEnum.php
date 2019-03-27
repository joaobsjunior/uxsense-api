<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

abstract class MessageEnum {
    /* TITLES */
    const titleErrorConsulta = 'Erro na consulta SQL';
    /* MESSAGES SERVER */
    const server401 = 'Acesso não autorizado';
    const server403 = 'Erro na autenticação, verifique os dados e tente novamente mais tarde';
    const server412 = 'Existem dados esperados que não foram recebidos pelo servidor';
    const server422 = 'Erro na execução do serviço';
    const server500 = 'O servidor não conseguiu completar sua requisição, verifique os dados enviados e tente novamente mais tarde.';
    /* MESSAGES SYSTEM */
    const msg01 = 'Erro na autenticação';
    const msg02 = 'Campo com o tamanho diferente do esperado';
    const msg03 = 'Autenticado mas não autorizado';
}
