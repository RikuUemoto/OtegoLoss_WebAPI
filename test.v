module test (
    input   CLK, RST,
    output  a
);

assign  a = CLK & RST;
    
endmodule