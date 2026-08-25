NOME_PROCESSO="processo_terminal_php=1"
DIRETORIO_EXECUCAO="/home/proscore/projeto_mvc_teste" # Altere para o diretório real do seu index.php
ARQUIVO_LOG="/home/proscore/projeto_mvc_teste/outputLogPhps07-8.out"

PHP=`which php`
NOHUP=`which nohup`
ECHO=`which echo`
CD=`which cd`

# --- VERIFICAÇÃO E EXECUÇÃO ---
if pgrep -f "$NOME_PROCESSO" > /dev/null
then
    $ECHO "[$($ECHO $(date))] O processo '$NOME_PROCESSO' está em execução."
else
    $ECHO "[$($ECHO $(date))] ALERTA: O processo '$NOME_PROCESSO' parou! Reiniciando..."
    
    # Acessa o diretório correto antes de rodar o PHP
    $CD "$DIRETORIO_EXECUCAO"
    
    
    $NOHUP env processo_terminal_php=1 $PHP index.php > "$ARQUIVO_LOG" 2>&1 &
fi