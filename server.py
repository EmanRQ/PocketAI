import uvicorn
from fastapi import FastAPI
from vllm import LLM, SamplingParams
from pydantic import BaseModel

app = FastAPI()

llm = LLM(model="meta-llama/Meta-Llama-3-8B-Instruct", tensor_parallel_size=1, enforce_eager=True)

class UserInput(BaseModel):
    prompt: str

@app.post("/chat")
async def chat(user_input: UserInput):
    # Prompt updated to be more Malay-centric/rojak
    system_prompt = (
        "You are a 'Financial Bestie' for Malaysian Gen Z students and young adults. "
        "Language: Gunakan Bahasa Melayu yang santai dan rojak (campur English). "
        "Tone: Chill, supportive, tapi tegas kalau pasal duit. Jangan jadi robot. "
        "Slang: Guna perkataan seperti 'no cap', 'slay', 'red flag', 'bruh', 'poyos', 'pokai'. "
        "Requirement: Validate setiap perbelanjaan sebagai 'red flag' atau 'green flag'. "
        "Format: Sentiasa tamatkan respon dengan struktur ini:\n"
        "### Current Financial Ring\n"
        "Summary: [Nasihat ringkas dalam BM]\n"
        "Data: {\"item\": \"nama_barang\", \"amount\": number}"
    )
    
    full_prompt = (
        f"<|begin_of_text|><|start_header_id|>system<|end_header_id|>\n{system_prompt}<|eot_id|>"
        f"<|start_header_id|>user<|end_header_id|>\n{user_input.prompt}<|eot_id|>"
        f"<|start_header_id|>assistant<|end_header_id|>\n"
    )
    
    sampling_params = SamplingParams(temperature=0.7, max_tokens=600)
    outputs = llm.generate([full_prompt], sampling_params)
    
    return {"response": outputs[0].outputs[0].text}

if __name__ == "__main__":
    uvicorn.run(app, host="0.0.0.0", port=8000)
